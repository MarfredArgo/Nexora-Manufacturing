<?php

namespace App\Console\Commands;

use App\Models\InventoryDeptCategory;
use App\Models\InventoryDeptItem;
use App\Models\InventoryDeptStockLevel;
use App\Models\InventoryDeptWarehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncInventoryData extends Command
{
    protected $signature = 'sync:inventory';

    protected $description = 'Pull categories, warehouses, items, and stock levels from the Inventory department database';

    protected const CHUNK_SIZE = 500;

    public function handle(): int
    {
        $this->info('Syncing inventory_dept -> local tables...');

        // inventory_dept_categories only has source_id + name (see migration
        // 2026_07_15_052222_create_inventory_dept_tables.php) — no source
        // timestamps on this table.
        $categoriesSynced = $this->syncModelTable(
            'categories',
            InventoryDeptCategory::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
            ],
            ['name']
        );
        $this->info("Synced {$categoriesSynced} categories.");

        // inventory_dept_warehouses columns: source_id, name, province, city,
        // barangay, address_description, country, capacity_units, status.
        // No source_created_at/source_updated_at on this table.
        $warehousesSynced = $this->syncModelTable(
            'warehouses',
            InventoryDeptWarehouse::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
                'province' => $row->province ?? null,
                'city' => $row->city ?? null,
                'barangay' => $row->barangay ?? null,
                'address_description' => $row->address_description ?? null,
                'country' => $row->country ?? null,
                'capacity_units' => $row->capacity_units ?? null,
                'status' => $row->status ?? null,
            ],
            ['name', 'province', 'city', 'barangay', 'address_description', 'country', 'capacity_units', 'status']
        );
        $this->info("Synced {$warehousesSynced} warehouses.");

        // inventory_dept_items columns: source_id, sku, name,
        // source_category_id, unit_cost. No source timestamps.
        $itemsSynced = $this->syncModelTable(
            'items',
            InventoryDeptItem::class,
            fn($row) => [
                'source_id' => $row->id,
                'sku' => $row->sku ?? '',
                'name' => $row->name,
                'source_category_id' => $row->category_id ?? null,
                'unit_cost' => $row->unit_cost ?? 0,
            ],
            ['sku', 'name', 'source_category_id', 'unit_cost']
        );
        $this->info("Synced {$itemsSynced} items.");

        // inventory_dept_stock_levels columns: source_id, source_item_id,
        // source_warehouse_id, quantity_on_hand, quantity_reserved,
        // reorder_threshold. No source timestamps.
        $stockLevelsSynced = $this->syncModelTable(
            'stock_levels',
            InventoryDeptStockLevel::class,
            fn($row) => [
                'source_id' => $row->id,
                'source_item_id' => $row->item_id ?? null,
                'source_warehouse_id' => $row->warehouse_id ?? null,
                'quantity_on_hand' => $row->stock,
                'quantity_reserved' => $row->quantity_reserved ?? 0,
                'reorder_threshold' => $row->reorder_threshold ?? 0,
            ],
            ['source_item_id', 'source_warehouse_id', 'quantity_on_hand', 'quantity_reserved', 'reorder_threshold']
        );
        $this->info("Synced {$stockLevelsSynced} stock levels.");

        // NOTE: there is no local migration/model for stock_movements or
        // stock_receivings — those blocks were removed. If you want to sync
        // them, add a migration + model for them first (see the finance
        // expenses/sales pattern in SyncFinanceData for an example), then
        // add a syncModelTable() call here.

        $this->info('Inventory sync complete.');

        return self::SUCCESS;
    }

    /**
     * @param  \Closure(object): array<string, mixed>  $mapRow
     * @param  array<int, string>  $updateColumns
     */
    protected function syncModelTable(string $sourceTable, string $modelClass, \Closure $mapRow, array $updateColumns): int
    {
        $synced = 0;

        DB::connection('inventory_dept')
            ->table($sourceTable)
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($rows) use ($mapRow, $modelClass, $updateColumns, &$synced) {
                $batch = $rows->map($mapRow)->all();

                if ($batch !== []) {
                    $modelClass::upsert($batch, ['source_id'], $updateColumns);
                }

                $synced += count($batch);
            });

        return $synced;
    }
}
