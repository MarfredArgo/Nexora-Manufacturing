<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('inventory');

        if (! $schema->hasTable('categories')) {
            $schema->create('categories', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('name', 100);
                $table->timestamps();
                $table->unique(['client_id', 'name']);
            });
        }

        if (! $schema->hasTable('warehouses')) {
            $schema->create('warehouses', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('name');
                $table->text('address')->nullable();
                $table->integer('capacity_units');
                $table->string('status', 20)->default('active');
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamp('deactivated_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['client_id', 'name']);
            });
        }

        if (! $schema->hasTable('items')) {
            $schema->create('items', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('sku', 50);
                $table->string('name');
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->decimal('unit_cost', 12, 2);
                $table->timestamps();
                $table->unique(['client_id', 'sku']);
            });
        }

        if (! $schema->hasTable('stock_levels')) {
            $schema->create('stock_levels', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->integer('stock')->default(0);
                $table->integer('reserved_quantity')->default(0);
                $table->integer('reorder_threshold')->default(0);
                $table->timestamps();
                $table->unique(['client_id', 'item_id', 'warehouse_id']);
            });
        }

        if (! $schema->hasTable('stock_movements')) {
            $schema->create('stock_movements', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('type', 20);
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->integer('quantity');
                $table->string('reference', 100)->nullable();
                $table->string('reference_id')->nullable()->index();
                $table->unsignedBigInteger('performed_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! $schema->hasTable('stock_adjustments')) {
            $schema->create('stock_adjustments', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->string('type', 20);
                $table->integer('quantity');
                $table->string('reason', 50);
                $table->string('status', 20)->default('pending');
                $table->unsignedBigInteger('requested_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('stock_transfers')) {
            $schema->create('stock_transfers', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('from_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->foreignId('to_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->integer('quantity');
                $table->string('status')->default('pending');
                $table->unsignedBigInteger('requested_by')->nullable();
                $table->unsignedBigInteger('requested_by_user_id')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('stock_receivings')) {
            $schema->create('stock_receivings', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('shipment_number');
                $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
                $table->integer('quantity')->default(0);
                $table->string('status');
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->unique(['client_id', 'shipment_number', 'item_id']);
            });
        }

        if (! $schema->hasTable('packing_materials')) {
            $schema->create('packing_materials', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('name');
                $table->integer('stock_qty')->default(0);
                $table->integer('low_stock_threshold')->default(5);
                $table->boolean('is_box')->default(false);
                $table->string('box_size')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('order_reservations')) {
            $schema->create('order_reservations', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('order_reference', 100);
                $table->string('source', 50)->default('api');
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->integer('quantity');
                $table->string('status', 20)->default('reserved');
                $table->timestamp('reserved_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
                $table->index(['client_id', 'order_reference', 'status']);
            });
        }
    }

    public function down(): void
    {
        // Inventory data is client-owned. Do not destroy it on rollback.
    }
};
