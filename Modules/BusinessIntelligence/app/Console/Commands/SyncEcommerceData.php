<?php

namespace App\Console\Commands;

use App\Models\EcommerceDeptConfiguratorConfig;
use App\Models\EcommerceDeptPrebuiltConfig;
use App\Models\EcommerceDeptProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncEcommerceData extends Command
{
    protected $signature = 'sync:ecommerce';

    protected $description = 'Sync Ecommerce department data';

    /**
     * Rows are pulled in chunks and written with a single upsert() per
     * chunk instead of one updateOrCreate() per row, so a table with
     * N rows takes ceil(N/500) queries instead of N queries.
     */
    protected const CHUNK_SIZE = 500;

    public function handle(): int
    {
        $this->info('Syncing ecommerce_dept -> local tables...');

        $productsSynced = $this->syncTable(
            'gaminglaptops',
            EcommerceDeptProduct::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
                'category' => 'Gaming Laptop',
                'brand' => $row->brand,
                'processor' => $row->processor,
                'specs' => isset($row->display) ? 'Display: ' . $row->display : null,
                'price' => $row->price,
                'original_price' => null,
                'rating' => null,
                'image_url' => $row->image_url,
                'badge' => null,
                'tag' => null,
                'os' => null,
                'cpu' => null,
                'gpu' => $row->gpu,
                'ram' => $row->ram,
                'storage' => $row->storage,
                'motherboard' => null,
                'psu' => null,
                'case' => null,
                'cooler' => null,
                'is_sold_out' => $row->is_sold_out,
                'forge_points' => 0,
                'shipping_status' => null,
                'promo_tag' => null,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            [
                'name',
                'category',
                'brand',
                'processor',
                'specs',
                'price',
                'original_price',
                'rating',
                'image_url',
                'badge',
                'tag',
                'os',
                'cpu',
                'gpu',
                'ram',
                'storage',
                'motherboard',
                'psu',
                'case',
                'cooler',
                'is_sold_out',
                'forge_points',
                'shipping_status',
                'promo_tag',
                'source_created_at',
                'source_updated_at',
            ]
        );
        $this->info("Synced {$productsSynced} products.");

        $prebuiltSynced = $this->syncTable(
            'prebuilt_configs',
            EcommerceDeptPrebuiltConfig::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
                'description' => $row->description,
                'price' => $row->price,
                'image_url' => $row->image_url,
                'cpu_id' => $row->cpu_id,
                'gpu_id' => $row->gpu_id,
                'motherboard_id' => $row->motherboard_id,
                'ram_id' => $row->ram_id,
                'storage_id' => $row->storage_id,
                'power_supply_id' => $row->power_supply_id,
                'pc_case_id' => $row->pc_case_id,
                'cooler_id' => $row->cooler_id,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            [
                'name',
                'description',
                'price',
                'image_url',
                'cpu_id',
                'gpu_id',
                'motherboard_id',
                'ram_id',
                'storage_id',
                'power_supply_id',
                'pc_case_id',
                'cooler_id',
                'source_created_at',
                'source_updated_at',
            ]
        );
        $this->info("Synced {$prebuiltSynced} prebuilt configs.");

        $configsSynced = $this->syncTable(
            'configurator_configs',
            EcommerceDeptConfiguratorConfig::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
                'description' => $row->description,
                'price' => $row->price,
                'image_url' => $row->image_url,
                'platform' => $row->platform,
                'tier' => $row->tier,
                'cpu_id' => $row->cpu_id,
                'gpu_id' => $row->gpu_id,
                'motherboard_id' => $row->motherboard_id,
                'ram_id' => $row->ram_id,
                'storage_id' => $row->storage_id,
                'power_supply_id' => $row->power_supply_id,
                'pc_case_id' => $row->pc_case_id,
                'cooler_id' => $row->cooler_id,
                'rating' => $row->rating,
                'review_count' => $row->review_count,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            [
                'name',
                'description',
                'price',
                'image_url',
                'platform',
                'tier',
                'cpu_id',
                'gpu_id',
                'motherboard_id',
                'ram_id',
                'storage_id',
                'power_supply_id',
                'pc_case_id',
                'cooler_id',
                'rating',
                'review_count',
                'source_created_at',
                'source_updated_at',
            ]
        );
        $this->info("Synced {$configsSynced} configurator configs.");

        $this->info('Ecommerce sync complete.');

        return self::SUCCESS;
    }

    /**
     * Pull a source table in chunks and upsert each chunk in one query.
     *
     * @param  \Closure(object): array<string, mixed>  $mapRow
     * @param  array<int, string>  $updateColumns
     */
    protected function syncTable(string $sourceTable, string $modelClass, \Closure $mapRow, array $updateColumns): int
    {
        $synced = 0;

        DB::connection('ecommerce_dept')
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