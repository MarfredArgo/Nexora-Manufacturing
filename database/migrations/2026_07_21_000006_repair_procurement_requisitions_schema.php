<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Repair an inconsistent legacy Procurement migration history. Some
     * deployments recorded the module migrations without ever creating the
     * requisitions tables, leaving the dashboard unable to load.
     */
    public function up(): void
    {
        $schema = Schema::connection('procurement');

        if (! $schema->hasTable('requisitions')) {
            $schema->create('requisitions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->string('req_number');
                $table->string('item');
                $table->unsignedInteger('qty')->default(1);
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('uom')->nullable();
                $table->string('delivery_status')->default('pending');
                $table->string('department')->nullable();
                $table->string('requested_by')->nullable();
                $table->string('status')->default('pending');
                $table->date('date_requested');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } elseif (! $schema->hasColumn('requisitions', 'client_id')) {
            $schema->table('requisitions', function (Blueprint $table): void {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            });
        }

        if (! $schema->hasTable('requisition_items')) {
            $schema->create('requisition_items', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->unsignedBigInteger('requisition_id');
                $table->unsignedBigInteger('supplier_product_id')->nullable();
                $table->string('name');
                $table->unsignedInteger('qty')->default(1);
                $table->string('uom')->nullable();
                $table->decimal('unit_price', 14, 2)->default(0);
                $table->decimal('amount', 14, 2)->default(0);
                $table->timestamps();
            });
        } elseif (! $schema->hasColumn('requisition_items', 'client_id')) {
            $schema->table('requisition_items', function (Blueprint $table): void {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // This is a data-preserving production repair; never drop the tables
        // or client boundaries during a routine rollback.
    }
};
