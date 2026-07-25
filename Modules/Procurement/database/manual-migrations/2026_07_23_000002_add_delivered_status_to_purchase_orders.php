<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (! Schema::connection('procurement')->hasTable('purchase_orders')) {
            return;
        }

        // Add 'delivered' to the allowed PO statuses so a shipment being
        // delivered can cascade to its parent purchase order.
        DB::connection('procurement')->statement(
            'ALTER TABLE purchase_orders DROP CONSTRAINT IF EXISTS purchase_orders_status_check'
        );
        DB::connection('procurement')->statement(<<<SQL
            ALTER TABLE purchase_orders
            ADD CONSTRAINT purchase_orders_status_check CHECK (
                status IN ('pending', 'processing', 'approved', 'rejected', 'cancelled', 'completed', 'delivered')
            )
        SQL);
    }

    public function down(): void
    {
        // Leave the widened constraint in place; narrowing it could reject
        // existing 'delivered' rows.
    }
};
