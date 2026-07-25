<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\OrderFulfillment;
use Modules\Inventory\Models\OrderReservation;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ErpIntegrationService;

class ItemCatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $warehouses = Warehouse::where('status', 'active')->get();

        $query = Item::with(['category', 'stockLevels.warehouse']);

        if ($search = $request->input('search')) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        if ($warehouse = $request->input('warehouse')) {
            $query->whereHas('stockLevels', fn ($q) => $q->where('warehouse_id', $warehouse));
        }

        if ($status = $request->input('status')) {
            match ($status) {
                'Out of Stock' => $query->outOfStock(),
                'Low Stock' => $query->lowStock(),
                'In Stock' => $query->inStock(),
                default => null,
            };
        }

        $items = $query->get();

        $allLevels = StockLevel::all();

        $items = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'sku' => $item->sku,
                'name' => $item->name,
                'category' => $item->category?->name ?? 'â€”',
                'warehouses' => $item->stockLevels->filter(fn ($sl) => $sl->stock > 0 && $sl->warehouse)->map(fn ($sl) => $sl->warehouse->name)->implode(', '),
                'total_available' => $item->stockLevels->sum('stock') - $item->stockLevels->sum('reserved_quantity'),
                'total_stock' => $item->stockLevels->sum('stock'),
                'unit_cost' => $item->unit_cost,
                'status' => $item->status,
                'stock_breakdown' => $item->stockLevels->filter(fn ($sl) => $sl->warehouse)->map(fn ($sl) => [
                    'stock_level_id' => $sl->id,
                    'warehouse' => $sl->warehouse->name,
                    'available' => $sl->stock - $sl->reserved_quantity,
                    'on_hand' => $sl->stock,
                    'reserved' => $sl->reserved_quantity,
                    'reorder_threshold' => $sl->reorder_threshold,
                    'status' => $sl->status_label,
                ])->values()->toArray(),
            ];
        });

        $page = $request->input('page', 1);
        $perPage = 10;
        $paginated = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $packingMaterials = OrderFulfillment::orderByDesc('created_at')->get();

        return view('inventory::item-catalog', [
            'items' => $paginated,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'packingMaterials' => $packingMaterials,
            'inStockCount' => $allLevels->filter(fn ($l) => $l->status === 'in_stock')->count(),
            'lowStockCount' => $allLevels->filter(fn ($l) => $l->status === 'low_stock')->count(),
            'outOfStockCount' => $allLevels->filter(fn ($l) => $l->status === 'out_of_stock')->count(),
            'activePage' => 'item-catalog',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:inventory.items,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:inventory.categories,id',
            'unit_cost' => 'required|numeric|min:0',
            'warehouse_id' => 'nullable|exists:inventory.warehouses,id',
            'initial_stock' => 'nullable|integer|min:0',
            'reorder_threshold' => 'nullable|integer|min:0',
        ]);

        $item = Item::create([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'unit_cost' => $validated['unit_cost'],
        ]);

        if (!empty($validated['warehouse_id']) && ($validated['initial_stock'] ?? 0) > 0) {
            StockLevel::create([
                'item_id' => $item->id,
                'warehouse_id' => $validated['warehouse_id'],
                'stock' => $validated['initial_stock'],
                'reserved_quantity' => 0,
                'reorder_threshold' => $validated['reorder_threshold'] ?? 10,
            ]);

            StockMovement::create([
                'type' => 'inbound',
                'item_id' => $item->id,
                'warehouse_id' => $validated['warehouse_id'],
                'quantity' => $validated['initial_stock'],
                'reference' => 'INIT-' . $item->sku,
                'notes' => 'Initial stock on creation',
                'performed_by' => session('employee_id'),
                'created_at' => now(),
            ]);
        }

        try {
            app(ErpIntegrationService::class)->inventoryAvailabilityChanged(
                (int) session('employee_client_id'),
                (int) $item->id,
                'inventory.item_created'
            );
        } catch (\Throwable $e) {
            // Ecommerce/BI credentials may be unconfigured — non-blocking
        }

        return redirect()->route('inventory.item-catalog')->with('success', "Item '{$item->sku}' created successfully.");
    }

    public function verifyPassword(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $employeeId = session('employee_id');
        if (!$employeeId) {
            return response()->json(['error' => 'No authenticated employee session.'], 403);
        }

        $employee = \Illuminate\Support\Facades\DB::connection('hr')
            ->table('employees')
            ->where('id', $employeeId)
            ->first();

        if (!$employee || !$employee->temporary_password) {
            return response()->json(['error' => 'Employee record not found.'], 404);
        }

        $stored = (string) $employee->temporary_password;
        $valid = str_starts_with($stored, '$')
            ? \Illuminate\Support\Facades\Hash::check($request->password, $stored)
            : hash_equals($stored, $request->password);

        if (!$valid) {
            return response()->json(['error' => 'Incorrect password.'], 422);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, Item $item)
    {
        $reserved = OrderReservation::where('item_id', $item->id)
            ->where('status', 'reserved')
            ->exists();

        if ($reserved) {
            return back()->withErrors(['delete' => 'Cannot delete item with active reservations. Cancel reservations first.']);
        }

        $sku = $item->sku;
        $clientId = (int) session('employee_client_id');

        StockMovement::where('item_id', $item->id)->delete();
        StockAdjustment::where('item_id', $item->id)->delete();
        StockTransfer::where('item_id', $item->id)->delete();
        OrderReservation::where('item_id', $item->id)->delete();
        StockLevel::where('item_id', $item->id)->delete();
        DB::connection('inventory')->table('requisitions')
            ->where('notes', 'LIKE', '%[item_id:' . $item->id . ']%')
            ->delete();

        $item->delete();

        // Notified after the delete commits, so a failed transaction cannot leave
        // downstream modules believing the product is gone.
        try {
            app(ErpIntegrationService::class)->inventoryProductDeleted($clientId, $item);
        } catch (\Throwable $e) {
            // Ecommerce/BI credentials may be unconfigured — non-blocking
        }

        return redirect()->route('inventory.item-catalog')->with('success', "Item '{$sku}' deleted successfully.");
    }

    public function storePackingMaterial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock_qty' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'is_box' => 'boolean',
            'box_size' => 'nullable|string|max:50',
        ]);

        $validated['is_box'] = $request->boolean('is_box');

        OrderFulfillment::create($validated);

        return redirect()->route('inventory.item-catalog')->with('success', 'Packing material added.');
    }

    public function destroyPackingMaterial($id)
    {
        $material = OrderFulfillment::findOrFail($id);
        $material->delete();

        return redirect()->route('inventory.item-catalog')->with('success', 'Packing material deleted.');
    }
}
