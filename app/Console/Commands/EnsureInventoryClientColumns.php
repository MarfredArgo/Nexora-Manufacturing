<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureInventoryClientColumns extends Command
{
    protected $signature = 'inventory:ensure-client-columns';

    protected $description = 'Add client boundaries to existing Inventory tables on the dedicated Inventory database';

    public function handle(): int
    {
        $schema = Schema::connection('inventory');

        foreach ([
            'categories', 'warehouses', 'items', 'stock_levels', 'stock_movements',
            'stock_adjustments', 'stock_transfers', 'stock_receivings',
            'packing_materials', 'order_reservations',
        ] as $tableName) {
            if (! $schema->hasTable($tableName) || $schema->hasColumn($tableName, 'client_id')) {
                continue;
            }

            $schema->table($tableName, function (Blueprint $table): void {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            });

            $this->info("Added client_id to Inventory {$tableName}.");
        }

        return self::SUCCESS;
    }
}
