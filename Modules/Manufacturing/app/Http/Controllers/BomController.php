<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Item;
use Modules\Manufacturing\Models\ProductBom;

class BomController extends Controller
{
    public function index(): RedirectResponse
    {
        // BoMs now live in the single Work Orders → BoM tab on the dashboard.
        return redirect()->route('manufacturing.dashboard', ['page' => 'orders', 'sub' => 'boms']);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'integer'],
            'items.*.quantity_required' => ['required', 'integer', 'min:1'],
        ]);

        $clientId = (int) session('employee_client_id');
        abort_unless($clientId > 0, 403);

        $itemIds = collect($validated['items'])->pluck('inventory_item_id')->unique()->values();
        $inventoryItems = Item::with('category')->whereIn('id', $itemIds)->get()->keyBy('id');

        if ($inventoryItems->count() !== $itemIds->count()) {
            return back()->withErrors(['items' => 'Every BOM component must belong to this client inventory.'])->withInput();
        }

        // Section is derived from the components' category, not a stored type.
        $isPackaging = $inventoryItems->contains(function ($item) {
            $cat = strtolower((string) optional($item->category)->name);
            return str_contains($cat, 'packag') || str_contains($cat, 'packing');
        });

        DB::connection('manufacturing')->transaction(function () use ($validated, $inventoryItems): void {
            $bom = ProductBom::create([
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]);

            foreach ($validated['items'] as $component) {
                $item = $inventoryItems->get($component['inventory_item_id']);
                $bom->items()->create([
                    'inventory_item_id' => $item->id,
                    'item_sku' => $item->sku,
                    'item_name' => $item->name,
                    'quantity_required' => $component['quantity_required'],
                ]);
            }
        });

        return redirect()->route('manufacturing.dashboard', [
            'page' => 'orders',
            'sub' => 'boms',
            'bomType' => $isPackaging ? 'packaging' : 'prebuilt',
        ])->with('success', 'Bill of Materials created. It is now available to E-commerce.');
    }

    public function destroy(ProductBom $bom): RedirectResponse
    {
        $bom->items()->delete();
        $bom->delete();

        return redirect()->route('manufacturing.boms.index')->with('success', 'Bill of Materials removed.');
    }
}
