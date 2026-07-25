<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (! Schema::connection('procurement')->hasTable('purchase_order_items')) {
            return;
        }

        // Store each ordered item's category on its own line so "Spend by
        // Category" can be split per item instead of per PO's primary category.
        if (! Schema::connection('procurement')->hasColumn('purchase_order_items', 'category')) {
            Schema::connection('procurement')->table('purchase_order_items', function (Blueprint $table): void {
                $table->string('category')->nullable();
            });
        }

        // Backfill existing rows with their PO's stored category (the `brand`
        // column), so historical spend keeps grouping sensibly.
        DB::connection('procurement')->statement(<<<SQL
            UPDATE purchase_order_items poi
            SET category = po.brand
            FROM purchase_orders po
            WHERE poi.purchase_order_id = po.id
              AND (poi.category IS NULL OR poi.category = '')
              AND po.brand IS NOT NULL
              AND po.brand <> ''
        SQL);
    }

    public function down(): void
    {
        // Keep the column; dropping it would lose per-item category data.
    }
};
