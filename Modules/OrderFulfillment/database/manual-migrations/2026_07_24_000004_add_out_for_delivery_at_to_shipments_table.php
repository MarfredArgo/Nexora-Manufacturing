<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * This is deliberately additive and guarded: production databases have
     * shipments created before the delivery-timer feature existed.
     */
    public function up(): void
    {
        $schema = Schema::connection('order_fulfillment');

        if ($schema->hasTable('shipments') && ! $schema->hasColumn('shipments', 'out_for_delivery_at')) {
            $schema->table('shipments', function (Blueprint $table): void {
                $table->timestamp('out_for_delivery_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('order_fulfillment');

        if ($schema->hasTable('shipments') && $schema->hasColumn('shipments', 'out_for_delivery_at')) {
            $schema->table('shipments', function (Blueprint $table): void {
                $table->dropColumn('out_for_delivery_at');
            });
        }
    }
};
