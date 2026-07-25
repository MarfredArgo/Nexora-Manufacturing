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

        if ($schema->hasTable('returns')) {
            return;
        }

        $schema->create('returns', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('order_id');
            $table->string('customer_name');
            $table->string('product_name');
            $table->text('reason')->nullable();
            $table->string('status')->default('NEW');
            $table->string('resolution')->nullable();
            $table->date('due_date')->nullable();
            $table->text('address')->nullable();
            $table->decimal('refund_amount', 14, 2)->default(0);
            $table->timestamps();
            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::connection('order_fulfillment')->dropIfExists('returns');
    }
};
