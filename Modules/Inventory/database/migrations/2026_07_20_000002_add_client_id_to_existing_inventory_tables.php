<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
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
        }
    }

    public function down(): void
    {
        // Do not remove client boundaries from operational inventory data.
    }
};
