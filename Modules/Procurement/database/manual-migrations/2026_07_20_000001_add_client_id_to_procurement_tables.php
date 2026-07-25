<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        foreach ([
            'suppliers', 'supplier_products', 'requisitions', 'requisition_items',
            'purchase_orders', 'purchase_order_items', 'deliveries',
        ] as $tableName) {
            if (! Schema::connection('procurement')->hasTable($tableName) || Schema::connection('procurement')->hasColumn($tableName, 'client_id')) {
                continue;
            }

            Schema::connection('procurement')->table($tableName, function (Blueprint $table): void {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // Client keys protect tenant boundaries and must not be removed by a
        // routine rollback.
    }
};


