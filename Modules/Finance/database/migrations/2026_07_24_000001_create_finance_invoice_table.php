<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('finance');

        if ($schema->hasTable('invoice')) {
            return;
        }

        $schema->create('invoice', function (Blueprint $table): void {
            $table->bigIncrements('invoice_id');
            $table->unsignedBigInteger('nexora_client_id')->index();
            $table->uuid('order_id')->index();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('invoice_amount', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('shipping_fee', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('outstanding_amount', 14, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->text('payment_details')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('payment_status')->default('Unpaid');
            $table->string('status')->default('Pending');
            $table->date('payment_date')->nullable();
            $table->timestamps();

            $table->unique(['nexora_client_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('finance')->dropIfExists('invoice');
    }
};
