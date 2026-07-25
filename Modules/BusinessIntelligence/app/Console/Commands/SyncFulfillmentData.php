<?php

namespace App\Console\Commands;

use App\Models\FulfillmentDeliveryMan;
use App\Models\FulfillmentOrder;
use App\Models\FulfillmentPackingError;
use App\Models\FulfillmentPackingMaterial;
use App\Models\FulfillmentShipment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncFulfillmentData extends Command
{
    protected $signature = 'sync:fulfillment';

    protected $description = 'Pull orders, shipments, delivery men, packing errors, and packing materials from the Order Fulfillment department database';

    protected const CHUNK_SIZE = 500;

    public function handle(): int
    {
        $this->info('Syncing fulfillment_dept -> local tables...');

        $ordersSynced = $this->syncTable(
            'orders',
            FulfillmentOrder::class,
            fn($row) => [
                'source_id' => $row->id,
                'customer_name' => $row->customer_name,
                'product_name' => $row->product_name,
                'qty' => $row->qty,
                'status' => $row->status,
                'due_date' => $row->due_date,
                'address' => $row->address,
                'amount' => $row->amount,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            ['customer_name', 'product_name', 'qty', 'status', 'due_date', 'address', 'amount', 'source_created_at', 'source_updated_at']
        );
        $this->info("Synced {$ordersSynced} orders.");

        $shipmentsSynced = $this->syncTable(
            'shipments',
            FulfillmentShipment::class,
            fn($row) => [
                'source_id' => $row->id,
                'shipment_id' => $row->shipment_id,
                'order_id' => $row->order_id,
                'customer_name' => $row->customer_name,
                'product_name' => $row->product_name,
                'qty' => $row->qty,
                'courier' => $row->courier,
                'box_used' => $row->box_used,
                'tracking_number' => $row->tracking_number,
                'status' => $row->status,
                'address' => $row->address,
                'due_date' => $row->due_date,
                'amount' => $row->amount,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            [
                'shipment_id',
                'order_id',
                'customer_name',
                'product_name',
                'qty',
                'courier',
                'box_used',
                'tracking_number',
                'status',
                'address',
                'due_date',
                'amount',
                'source_created_at',
                'source_updated_at',
            ]
        );
        $this->info("Synced {$shipmentsSynced} shipments.");

        $deliveryMenSynced = $this->syncTable(
            'delivery_men',
            FulfillmentDeliveryMan::class,
            fn($row) => [
                'source_id' => $row->id,
                'age' => $row->age,
                'license_num' => $row->license_num,
                'vehicle_type' => $row->vehicle_type,
                'shipping_provider_id' => $row->shipping_provider_id,
            ],
            ['age', 'license_num', 'vehicle_type', 'shipping_provider_id']
        );
        $this->info("Synced {$deliveryMenSynced} delivery men.");

        $packingErrorsSynced = $this->syncTable(
            'packing_errors',
            FulfillmentPackingError::class,
            fn($row) => [
                'source_id' => $row->id,
                'order_id' => $row->order_id,
                'material' => $row->material,
                'reason' => $row->reason,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            ['order_id', 'material', 'reason', 'source_created_at', 'source_updated_at']
        );
        $this->info("Synced {$packingErrorsSynced} packing errors.");

        $packingMaterialsSynced = $this->syncTable(
            'packing_materials',
            FulfillmentPackingMaterial::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
                'stock_qty' => $row->stock_qty,
                'low_stock_threshold' => $row->low_stock_threshold,
                'is_box' => $row->is_box,
                'box_size' => $row->box_size,
            ],
            ['name', 'stock_qty', 'low_stock_threshold', 'is_box', 'box_size']
        );
        $this->info("Synced {$packingMaterialsSynced} packing materials.");

        $this->info('Fulfillment sync complete.');

        return self::SUCCESS;
    }

    /**
     * @param  \Closure(object): array<string, mixed>  $mapRow
     * @param  array<int, string>  $updateColumns
     */
    protected function syncTable(string $sourceTable, string $modelClass, \Closure $mapRow, array $updateColumns): int
    {
        $synced = 0;

        DB::connection('fulfillment_dept')
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
