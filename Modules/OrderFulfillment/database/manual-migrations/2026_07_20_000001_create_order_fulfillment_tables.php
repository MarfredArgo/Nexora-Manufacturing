<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        $schema = Schema::connection('order_fulfillment');
        $tables = [
            'orders' => function (Blueprint $table): void {
                $table->string('id')->primary(); $table->unsignedBigInteger('client_id')->index();
                $table->string('customer_name'); $table->string('product_name'); $table->unsignedInteger('qty')->default(1);
                $table->decimal('product_amount', 14, 2)->default(0); $table->string('status')->default('NEW');
                $table->text('address')->nullable(); $table->date('due_date')->nullable(); $table->timestamps();
            },
            'shipments' => function (Blueprint $table): void {
                $table->id(); $table->unsignedBigInteger('client_id')->index(); $table->string('shipment_id'); $table->string('order_id');
                $table->string('customer_name'); $table->string('product_name'); $table->unsignedInteger('qty'); $table->decimal('amount', 14, 2)->default(0);
                $table->string('courier')->nullable(); $table->string('box_used')->nullable(); $table->string('tracking_number')->nullable();
                $table->string('status')->default('SHIPPED'); $table->text('address')->nullable(); $table->date('due_date')->nullable();
                $table->string('delivery_man_id')->nullable(); $table->timestamp('shipped_at')->nullable(); $table->timestamp('out_for_delivery_at')->nullable(); $table->timestamps();
            },
            'packing_errors' => function (Blueprint $table): void {
                $table->id(); $table->unsignedBigInteger('client_id')->index(); $table->string('order_id'); $table->string('material'); $table->string('reason'); $table->timestamps();
            },
        ];
        foreach ($tables as $name => $create) { if (! $schema->hasTable($name)) { $schema->create($name, $create); } }
        if (! $schema->hasTable('requisitions')) {
            $schema->create('requisitions', function (Blueprint $table): void { $table->id(); $table->unsignedBigInteger('client_id')->index(); $table->string('req_number'); $table->string('item'); $table->unsignedInteger('qty'); $table->string('department')->nullable(); $table->string('requested_by')->nullable(); $table->date('date_requested'); $table->text('notes')->nullable(); $table->string('priority')->nullable(); $table->string('categories')->nullable(); $table->timestamps(); });
        }
    }

    public function down(): void {}
};
