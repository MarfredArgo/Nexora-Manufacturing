<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        // Driver profiles now belong to HR's delivery_drivers table. Shipment
        // records retain their assigned ID for history, but fulfillment no
        // longer owns a duplicate staff directory.
        Schema::connection('order_fulfillment')->dropIfExists('delivery_men');
    }

    public function down(): void
    {
        // The former table must not be recreated: HR is the sole owner.
    }
};
