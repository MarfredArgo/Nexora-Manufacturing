<?php

namespace Modules\Procurement\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureProcurementClientColumns extends Command
{
    protected $signature = 'procurement:ensure-client-columns';

    protected $description = 'Add client boundaries to existing Procurement tables on the dedicated Procurement database';

    public function handle(): int
    {
        $schema = Schema::connection('procurement');

        foreach ([
            'suppliers', 'supplier_products', 'requisitions', 'requisition_items',
            'purchase_orders', 'purchase_order_items', 'deliveries',
        ] as $tableName) {
            if (! $schema->hasTable($tableName) || $schema->hasColumn($tableName, 'client_id')) {
                continue;
            }

            $schema->table($tableName, function (Blueprint $table): void {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            });

            $this->info("Added client_id to Procurement {$tableName}.");
        }

        // These fields are used to route a supplier delivery to the correct
        // Inventory warehouse. This command runs on every deployment, unlike
        // a one-time Laravel migration record, so it also repairs Procurement
        // databases that were connected after the original migration ran.
        if ($schema->hasTable('purchase_orders')) {
            if (! $schema->hasColumn('purchase_orders', 'warehouse_id')) {
                $schema->table('purchase_orders', function (Blueprint $table): void {
                    $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                });

                $this->info('Added warehouse_id to Procurement purchase_orders.');
            }

            if (! $schema->hasColumn('purchase_orders', 'delivery_address')) {
                $schema->table('purchase_orders', function (Blueprint $table): void {
                    $table->string('delivery_address')->nullable();
                });

                $this->info('Added delivery_address to Procurement purchase_orders.');
            }
        }

        if ($schema->hasTable('suppliers') && ! $schema->hasColumn('suppliers', 'warehouse_id')) {
            $schema->table('suppliers', function (Blueprint $table): void {
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            });

            $this->info('Added warehouse_id to Procurement suppliers.');
        }

        // Deliveries record which Inventory warehouse the shipment is delivered to.
        if ($schema->hasTable('deliveries') && ! $schema->hasColumn('deliveries', 'deliver_to_warehouse')) {
            $schema->table('deliveries', function (Blueprint $table): void {
                $table->string('deliver_to_warehouse')->nullable();
            });

            $this->info('Added deliver_to_warehouse to Procurement deliveries.');
        }

        // Supplier product category (drives the PO modal's supplier -> category
        // -> item cascading selection).
        if ($schema->hasTable('supplier_products') && ! $schema->hasColumn('supplier_products', 'categories')) {
            $schema->table('supplier_products', function (Blueprint $table): void {
                $table->string('categories', 100)->nullable();
            });

            $this->info('Added categories to Procurement supplier_products.');
        }

        return self::SUCCESS;
    }
}
