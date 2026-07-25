<?php

namespace App\Services\Departments;

use App\Models\InventoryDeptCategory;
use App\Models\InventoryDeptItem;
use App\Models\InventoryDeptStockLevel;
use App\Models\InventoryDeptWarehouse;
use Illuminate\Support\Collection;

/**
 * Computes Inventory & Warehouse KPIs from the real data synced from the
 * Inventory department's own database (via `sync:inventory`).
 *
 * Their system tracks stock per item *per warehouse*
 * (inventory_dept_stock_levels), so "low stock" / "out of stock" are
 * evaluated at the item+warehouse level, then rolled up for the KPI
 * cards. Method names and return shapes match the previous
 * InventoryItem-based implementation so nothing else in the app needs
 * to change.
 */
class InventoryService
{
    /**
     * @return array<string, mixed>
     */
    public function getSnapshot(): array
    {
        return [
            'total_skus' => $this->totalSkus(),
            'low_stock_count' => $this->lowStockCount(),
            'out_of_stock_count' => $this->outOfStockCount(),
            'inventory_value' => $this->totalInventoryValue(),
            'low_stock_items' => $this->lowStockItems()->values()->toArray(),
            'out_of_stock_items' => $this->outOfStockItems()->values()->toArray(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getKpiSummaryForAi(): array
    {
        return [
            'total_skus' => $this->totalSkus(),
            'low_stock_count' => $this->lowStockCount(),
            'out_of_stock_count' => $this->outOfStockCount(),
            'inventory_value' => $this->totalInventoryValue(),
            'critical_items' => $this->lowStockItems()->take(5)->values()->toArray(),
            'out_of_stock_items' => $this->outOfStockItems()->take(5)->values()->toArray(),
        ];
    }

    public function totalSkus(): int
    {
        return InventoryDeptItem::count();
    }

    public function lowStockCount(): int
    {
        return InventoryDeptStockLevel::lowStock()->count();
    }

    public function outOfStockCount(): int
    {
        return InventoryDeptStockLevel::outOfStock()->count();
    }

    /**
     * Total value of all stock on hand across every warehouse,
     * valued at each item's unit cost.
     */
    public function totalInventoryValue(): float
    {
        return (float) InventoryDeptStockLevel::query()
            ->join('inventory_dept_items', 'inventory_dept_items.source_id', '=', 'inventory_dept_stock_levels.source_item_id')
            ->selectRaw('SUM(inventory_dept_stock_levels.quantity_on_hand * inventory_dept_items.unit_cost) as total')
            ->value('total') ?? 0.0;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function lowStockItems(): Collection
    {
        return $this->stockLevelDetails(
            InventoryDeptStockLevel::lowStock()->orderBy('quantity_on_hand')
        );
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function outOfStockItems(): Collection
    {
        return $this->stockLevelDetails(
            InventoryDeptStockLevel::outOfStock()
        );
    }

    /**
     * Available stock (on hand, minus what's already reserved) per
     * product name, summed across every warehouse. Matched by product
     * name since that's the only key shared with Order Fulfillment's
     * synced data.
     *
     * @param  array<int, string>  $productNames
     * @return Collection<string, int> keyed by product name
     */
    public function availableStockByProductName(array $productNames): Collection
    {
        if ($productNames === []) {
            return collect();
        }

        return InventoryDeptStockLevel::query()
            ->join('inventory_dept_items', 'inventory_dept_items.source_id', '=', 'inventory_dept_stock_levels.source_item_id')
            ->whereIn('inventory_dept_items.name', $productNames)
            ->selectRaw('inventory_dept_items.name as name, SUM(inventory_dept_stock_levels.quantity_on_hand) as on_hand, SUM(inventory_dept_stock_levels.quantity_reserved) as reserved')
            ->groupBy('inventory_dept_items.name')
            ->get()
            ->mapWithKeys(fn($row) => [$row->name => max(0, (int) $row->on_hand - (int) $row->reserved)]);
    }

    /**
     * Inventory Coverage = Available Stock / Average Monthly Sales.
     * Reorder thresholds are intentionally NOT used here.
     *
     * Stock Status is derived from the coverage percentage:
     *   > 100%    => Healthy
     *   50 - 100% => Moderate
     *   < 50%     => Low Stock
     *
     * @return array{coverage: int, stock_status: string, stock_class: string}
     */
    public function inventoryCoverage(float $availableStock, float $averageMonthlySales): array
    {
        if ($averageMonthlySales <= 0) {
            // No recent sales velocity to divide by. Any stock on hand is
            // reported as fully covered rather than an undefined/infinite
            // ratio; zero stock with zero sales is treated as Low Stock
            // so it doesn't silently disappear from attention.
            $coverage = $availableStock > 0 ? 100 : 0;
        } else {
            $coverage = (int) round(($availableStock / $averageMonthlySales) * 100);
        }

        [$status, $class] = match (true) {
            $coverage > 100 => ['Healthy', 'bg-low'],
            $coverage >= 50 => ['Moderate', 'bg-med'],
            default => ['Low Stock', 'bg-high'],
        };

        return [
            'coverage' => $coverage,
            'stock_status' => $status,
            'stock_class' => $class,
        ];
    }

    /**
     * Joins stock level rows with their item/category/warehouse
     * details, matching the shape the rest of the app already expects.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function stockLevelDetails($query): Collection
    {
        $itemsById = InventoryDeptItem::all()->keyBy('source_id');
        $categoriesById = InventoryDeptCategory::all()->keyBy('source_id');
        $warehousesById = InventoryDeptWarehouse::all()->keyBy('source_id');

        return $query->get()->map(function (InventoryDeptStockLevel $stock) use ($itemsById, $categoriesById, $warehousesById) {
            $item = $itemsById->get($stock->source_item_id);
            $category = $item ? $categoriesById->get($item->source_category_id) : null;
            $warehouse = $warehousesById->get($stock->source_warehouse_id);

            return [
                'sku' => $item->sku ?? 'Unknown',
                'name' => $item->name ?? 'Unknown item',
                'category' => $category->name ?? 'Uncategorized',
                'warehouse_zone' => $warehouse->name ?? 'Unknown warehouse',
                'quantity_on_hand' => $stock->quantity_on_hand,
                'reorder_threshold' => $stock->reorder_threshold,
                'unit_cost' => $item ? (float) $item->unit_cost : 0.0,
                'stock_shortage' => max(0, $stock->reorder_threshold - $stock->quantity_on_hand),
            ];
        });
    }
}
