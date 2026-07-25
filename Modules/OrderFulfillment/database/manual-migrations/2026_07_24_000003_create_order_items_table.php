<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'order_fulfillment';

    public function up(): void
    {
        $schema = Schema::connection('order_fulfillment');

        if ($schema->hasTable('order_items')) {
            return;
        }

        $schema->create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('order_id')->index();
            $table->string('product_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('product_amount', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('order_fulfillment')->dropIfExists('order_items');
    }
};
