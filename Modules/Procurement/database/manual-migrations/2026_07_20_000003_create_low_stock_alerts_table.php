<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('low_stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('external_item_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('threshold')->default(5);
            $table->integer('warehouse_id')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('low_stock_alerts');
    }
};
