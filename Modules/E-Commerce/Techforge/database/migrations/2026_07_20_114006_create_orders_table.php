<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('pending'); // pending, paid, processing, shipped, delivered, cancelled
            $table->decimal('total', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->string('payment_method');
            $table->string('payment_status')->default('unpaid');
            $table->json('shipping_address');
            $table->string('tracking_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
