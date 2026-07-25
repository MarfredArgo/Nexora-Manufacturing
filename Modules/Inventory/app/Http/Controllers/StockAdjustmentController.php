<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Controllers\Concerns\HasInventoryPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\ErpIntegrationService;

class StockAdjustmentController extends Controller
{
    use HasInventoryPermissions;
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['item', 'warehouse', 'requester', 'approver']);

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($reason = $request->input('reason')) {
            $query->where('reason', $reason);
        }

        if ($warehouse = $request->input('warehouse')) {
            $query->where('warehouse_id', $warehouse);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($iq) use ($search) {
                    $iq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        $adjustments = $query->orderByDesc('created_at')->paginate(10)->appends($request->query());

        $totalCount = StockAdjustment::count();
        $netAdjustedUnits = StockAdjustment::where('status', 'approved')
            ->selectRaw("SUM(CASE WHEN type = 'increase' THEN quantity ELSE -quantity END) as net")
            ->value('net') ?? 0;
        $pendingCount = StockAdjustment::where('status', 'pending')->count();

        $stockLevels = StockLevel::with('item')->get();

        $itemsByWarehouse = $stockLevels
            ->groupBy('warehouse_id')
            ->map(fn ($levels) => $levels->pluck('item')->filter()->unique('id')->values());

        $stockMap = $stockLevels->mapWithKeys(
            fn ($sl) => [$sl->warehouse_id . '-' . $sl->item_id => $sl->stock - $sl->reserved_quantity]
        );

        return view('inventory::stock-adjustments', [
            'adjustments' => $adjustments,
            'warehouses' => Warehouse::where('status', 'active')->whereNull('deleted_at')->get(),
            'items' => Item::all(),
            'itemsByWarehouse' => $itemsByWarehouse,
            'stockMap' => $stockMap,
            'filters' => $request->only(['search', 'type', 'reason', 'warehouse', 'status']),
            'totalCount' => $totalCount,
            'netAdjustedUnits' => $netAdjustedUnits,
            'pendingCount' => $pendingCount,
            'activePage' => 'stock-adjustments',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory.items,id',
            'warehouse_id' => ['required', Rule::exists('inventory.warehouses', 'id')->whereNull('deleted_at')->where('status', 'active')],
            'type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:damage,expired,recount,theft,correction',
            'notes' => 'nullable|string',
        ]);

        if ($validated['type'] === 'decrease') {
            $stockLevel = StockLevel::where('item_id', $validated['item_id'])
                ->where('warehouse_id', $validated['warehouse_id'])
                ->first();

            if (!$stockLevel) {
                return back()->withInput()->withErrors([
                    'quantity' => 'No stock record found for this item in the selected warehouse.'
                ]);
            }

            $available = $stockLevel->stock - $stockLevel->reserved_quantity;

            if ($available < $validated['quantity']) {
                return back()->withInput()->withErrors([
                    'quantity' => "Insufficient available stock. Only {$available} units available (stock: {$stockLevel->stock}, reserved: {$stockLevel->reserved_quantity})."
                ]);
            }
        }

        $validated['status'] = 'pending';
        $validated['requested_by'] = auth()->id();
        StockAdjustment::create($validated);

        return back()->with('success', 'Adjustment request submitted for approval.');
    }

    public function approve(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'This adjustment has already been processed.']);
        }

        if (! $this->isInventoryManager()) {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'Only Inventory Managers can approve adjustments.']);
        }

        $result = $this->executeApproval($adjustment);

        if ($result === true) {
            try {
                app(ErpIntegrationService::class)->inventoryAvailabilityChanged(
                    (int) session('employee_client_id'),
                    (int) $adjustment->item_id,
                    'inventory.adjustment_approved'
                );
            } catch (\Throwable $e) {
                // Ecommerce/BI credentials may be unconfigured — non-blocking
            }
            return back()->with('success', 'Adjustment approved and stock updated.');
        }

        return back()->withErrors(["adj_action_{$adjustment->id}" => $result]);
    }

    private function executeApproval(StockAdjustment $adjustment): true|string
    {
        $adjustment = StockAdjustment::lockForUpdate()->find($adjustment->id);

        if (! $adjustment) {
            return 'This adjustment no longer exists.';
        }

        if ($adjustment->status !== 'pending') {
            return 'This adjustment has already been processed.';
        }

        $warehouse = Warehouse::where('id', $adjustment->warehouse_id)
            ->whereNull('deleted_at')->where('status', 'active')->lockForUpdate()->first();

        if (!$warehouse) {
            return 'Warehouse is no longer active.';
        }

        $stockLevel = StockLevel::where('item_id', $adjustment->item_id)
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->lockForUpdate()
            ->first();

        if (!$stockLevel) {
            return 'No stock level record exists for this item and warehouse combination.';
        }

        if ($adjustment->type === 'decrease') {
            $available = $stockLevel->stock - $stockLevel->reserved_quantity;

            if ($available < $adjustment->quantity) {
                return "Insufficient available stock. Only {$available} units available (stock: {$stockLevel->stock}, reserved: {$stockLevel->reserved_quantity}).";
            }
        }

        if ($adjustment->type === 'increase') {
            $stockLevel->increment('stock', $adjustment->quantity);
        } else {
            $stockLevel->decrement('stock', $adjustment->quantity);
        }

        Warehouse::where('id', $adjustment->warehouse_id)
            ->update(['last_activity_at' => now()]);

        $adjustment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        StockMovement::create([
            'type' => 'adjustment',
            'item_id' => $adjustment->item_id,
            'warehouse_id' => $adjustment->warehouse_id,
            'quantity' => $adjustment->type === 'decrease' ? -$adjustment->quantity : $adjustment->quantity,
            'reference' => 'ADJ-' . now()->format('Y') . '-' . str_pad($adjustment->id, 4, '0', STR_PAD_LEFT),
            'notes' => "Adjustment #{$adjustment->id} approved: {$adjustment->type} ({$adjustment->reason})",
            'performed_by' => session('employee_id'),
            'created_at' => now(),
        ]);

        return true;
    }

    public function reject(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'This adjustment has already been processed.']);
        }

        if (! $this->isInventoryManager()) {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'Only Inventory Managers can reject adjustments.']);
        }

        $adjustmentId = $adjustment->id;
        $adjustment = StockAdjustment::lockForUpdate()->find($adjustmentId);

        if (! $adjustment) {
            return back()->withErrors(["adj_action_{$adjustmentId}" => 'This adjustment no longer exists.']);
        }

        if ($adjustment->status !== 'pending') {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'This adjustment has already been processed.']);
        }

        $adjustment->update(['status' => 'rejected']);

        return back()->with('success', 'Adjustment rejected.');
    }

    public function cancel(StockAdjustment $adjustment)
    {
        $adjustmentId = $adjustment->id;
        $adjustment = StockAdjustment::lockForUpdate()->find($adjustmentId);

        if (! $adjustment) {
            return back()->withErrors(["adj_action_{$adjustmentId}" => 'This adjustment no longer exists.']);
        }

        if ($adjustment->status !== 'pending') {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'Only pending adjustments can be cancelled.']);
        }

        if (! $this->canCancelRequest((int) $adjustment->requested_by)) {
            return back()->withErrors(["adj_action_{$adjustment->id}" => 'You can only cancel your own adjustment requests.']);
        }

        $adjustment->update(['status' => 'cancelled']);

        return back()->with('success', 'Adjustment request cancelled.');
    }
}
