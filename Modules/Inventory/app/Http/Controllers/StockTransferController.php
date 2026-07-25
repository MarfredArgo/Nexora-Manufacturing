<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Controllers\Concerns\HasInventoryPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockTransferController extends Controller
{
    use HasInventoryPermissions;
    public function index(Request $request)
    {
        $query = StockTransfer::with(['item', 'fromWarehouse', 'toWarehouse', 'approver', 'requester']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($fromWarehouse = $request->input('from_warehouse')) {
            $query->where('from_warehouse_id', $fromWarehouse);
        }

        if ($toWarehouse = $request->input('to_warehouse')) {
            $query->where('to_warehouse_id', $toWarehouse);
        }

        if ($search = $request->input('search')) {
            $search = strtolower($search);
            $query->whereHas('item', function ($iq) use ($search) {
                $iq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
            });
        }

        $transfers = $query->orderByDesc('created_at')->paginate(10)->appends($request->query());

        $totalCount = StockTransfer::count();
        $pendingCount = StockTransfer::where('status', 'pending')->count();
        $approvedCount = StockTransfer::where('status', 'approved')->count();

        $stockLevels = StockLevel::with('item')->get();

        $itemsByWarehouse = $stockLevels
            ->filter(fn ($sl) => ($sl->stock - $sl->reserved_quantity) > 0)
            ->groupBy('warehouse_id')
            ->map(fn ($levels) => $levels->pluck('item')->filter()->unique('id')->values());

        $stockMap = $stockLevels->mapWithKeys(
            fn ($sl) => [$sl->warehouse_id . '-' . $sl->item_id => $sl->stock - $sl->reserved_quantity]
        );

        return view('inventory::stock-transfers', [
            'transfers' => $transfers,
            'warehouses' => Warehouse::where('status', 'active')->whereNull('deleted_at')->get(),
            'items' => Item::all(),
            'itemsByWarehouse' => $itemsByWarehouse,
            'stockMap' => $stockMap,
            'filters' => $request->only(['search', 'status', 'from_warehouse', 'to_warehouse']),
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'activePage' => 'stock-transfers',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory.items,id',
            'from_warehouse_id' => ['required', Rule::exists('inventory.warehouses', 'id')->whereNull('deleted_at')->where('status', 'active')],
            'to_warehouse_id' => ['required', Rule::exists('inventory.warehouses', 'id')->whereNull('deleted_at')->where('status', 'active'), 'different:from_warehouse_id'],
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Enforce quantity <= available stock in the selected source warehouse.
        $stockLevel = StockLevel::where('item_id', $validated['item_id'])
            ->where('warehouse_id', $validated['from_warehouse_id'])
            ->first();

        if (!$stockLevel) {
            return back()
                ->withErrors(['quantity' => 'No stock level record exists for the selected source warehouse.'])
                ->withInput();
        }

        $available = $stockLevel->stock - $stockLevel->reserved_quantity;

        if ($available < $validated['quantity']) {
            return back()
                ->withErrors(['quantity' => "Insufficient available stock in source warehouse. Only {$available} units available (stock: {$stockLevel->stock}, reserved: {$stockLevel->reserved_quantity})."])
                ->withInput();
        }

        $validated['status'] = 'pending';
        $validated['requested_by'] = auth()->id();

        StockTransfer::create($validated);

        return back()->with('success', 'Transfer request submitted for approval.');
    }

    public function approve(StockTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'This transfer has already been processed.']);
        }

        if (! $this->isInventoryManager()) {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'Only Inventory Managers can approve transfers.']);
        }

        $result = $this->executeApproval($transfer);

        if ($result === true) {
            return back()->with('success', 'Transfer approved and stock moved.');
        }

        return back()->withErrors(["trf_action_{$transfer->id}" => $result]);
    }

    private function executeApproval(StockTransfer $transfer): true|string
    {
        $transfer = StockTransfer::lockForUpdate()->find($transfer->id);

        if (! $transfer) {
            return 'This transfer no longer exists.';
        }

        if ($transfer->status !== 'pending') {
            return 'This transfer has already been processed.';
        }

        $fromWarehouse = Warehouse::where('id', $transfer->from_warehouse_id)
            ->whereNull('deleted_at')->where('status', 'active')->lockForUpdate()->first();
        $toWarehouse = Warehouse::where('id', $transfer->to_warehouse_id)
            ->whereNull('deleted_at')->where('status', 'active')->lockForUpdate()->first();

        if (!$fromWarehouse) {
            return 'Source warehouse is no longer active.';
        }

        if (!$toWarehouse) {
            return 'Destination warehouse is no longer active.';
        }

        $source = StockLevel::where('item_id', $transfer->item_id)
            ->where('warehouse_id', $transfer->from_warehouse_id)
            ->lockForUpdate()
            ->first();

        $destination = StockLevel::where('item_id', $transfer->item_id)
            ->where('warehouse_id', $transfer->to_warehouse_id)
            ->lockForUpdate()
            ->first();

        if (!$source) {
            return 'No stock level record exists for the source warehouse.';
        }

        $available = $source->stock - $source->reserved_quantity;

        if ($available < $transfer->quantity) {
            return "Insufficient available stock in source warehouse. Only {$available} units available (stock: {$source->stock}, reserved: {$source->reserved_quantity}).";
        }

        if (!$destination) {
            try {
                $destination = StockLevel::create([
                    'item_id' => $transfer->item_id,
                    'warehouse_id' => $transfer->to_warehouse_id,
                    'stock' => 0,
                    'reorder_threshold' => $source->reorder_threshold,
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                $destination = StockLevel::where('item_id', $transfer->item_id)
                    ->where('warehouse_id', $transfer->to_warehouse_id)
                    ->lockForUpdate()
                    ->first();
            }
        }

        if (! $destination) {
            return 'Failed to create or find destination stock level.';
        }

        $reference = $transfer->reference;
        $now = now();

        $source->decrement('stock', $transfer->quantity);

        $destination->increment('stock', $transfer->quantity);

        Warehouse::whereIn('id', [$transfer->from_warehouse_id, $transfer->to_warehouse_id])
            ->update(['last_activity_at' => $now]);

        StockMovement::create([
            'type' => 'transfer',
            'item_id' => $transfer->item_id,
            'warehouse_id' => $transfer->from_warehouse_id,
            'quantity' => $transfer->quantity,
            'reference' => $reference,
            'notes' => "Transfer #{$transfer->id} from {$transfer->fromWarehouse->name} to {$transfer->toWarehouse->name}",
            'performed_by' => session('employee_id'),
            'created_at' => $now,
        ]);

        StockMovement::create([
            'type' => 'transfer',
            'item_id' => $transfer->item_id,
            'warehouse_id' => $transfer->to_warehouse_id,
            'quantity' => $transfer->quantity,
            'reference' => $reference,
            'notes' => "Transfer #{$transfer->id} from {$transfer->fromWarehouse->name} to {$transfer->toWarehouse->name}",
            'performed_by' => session('employee_id'),
            'created_at' => $now,
        ]);

        $transfer->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => $now,
        ]);

        return true;
    }

    public function reject(StockTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'This transfer has already been processed.']);
        }

        if (! $this->isInventoryManager()) {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'Only Inventory Managers can reject transfers.']);
        }

        $transferId = $transfer->id;
        $transfer = StockTransfer::lockForUpdate()->find($transferId);

        if (! $transfer) {
            return back()->withErrors(["trf_action_{$transferId}" => 'This transfer no longer exists.']);
        }

        if ($transfer->status !== 'pending') {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'This transfer has already been processed.']);
        }

        $transfer->update(['status' => 'rejected']);

        return back()->with('success', 'Transfer rejected.');
    }

    public function cancel(StockTransfer $transfer)
    {
        $transferId = $transfer->id;
        $transfer = StockTransfer::lockForUpdate()->find($transferId);

        if (! $transfer) {
            return back()->withErrors(["trf_action_{$transferId}" => 'This transfer no longer exists.']);
        }

        if ($transfer->status !== 'pending') {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'Only pending transfers can be cancelled.']);
        }

        if (! $this->canCancelRequest((int) $transfer->requested_by)) {
            return back()->withErrors(["trf_action_{$transfer->id}" => 'You can only cancel your own transfer requests.']);
        }

        $transfer->update(['status' => 'cancelled']);

        return back()->with('success', 'Transfer request cancelled.');
    }
}

