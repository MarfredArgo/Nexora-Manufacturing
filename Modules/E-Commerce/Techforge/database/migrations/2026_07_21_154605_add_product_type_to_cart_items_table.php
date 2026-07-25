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
        Schema::connection('ecommerce')->table('cart_items', function (Blueprint $table) {
            $table->string('product_type')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('ecommerce')->table('cart_items', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};
