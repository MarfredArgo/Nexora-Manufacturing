<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('manufacturing');

        if (! $schema->hasTable('product_boms')) {
            $schema->create('product_boms', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('sku', 100);
                $table->string('name', 160);
                $table->text('description')->nullable();
                $table->string('status', 30)->default('active');
                $table->timestamps();
                $table->unique(['client_id', 'sku']);
            });
        }

        if (! $schema->hasTable('product_bom_items')) {
            $schema->create('product_bom_items', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->unsignedBigInteger('bom_id')->index();
                // Inventory lives in its own database, so this deliberately
                // remains an application-level reference rather than an FK.
                $table->unsignedBigInteger('inventory_item_id');
                $table->string('item_sku', 100)->nullable();
                $table->string('item_name', 160);
                $table->unsignedInteger('quantity_required')->default(1);
                $table->timestamps();
                $table->unique(['bom_id', 'inventory_item_id']);
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('manufacturing');
        $schema->dropIfExists('product_bom_items');
        $schema->dropIfExists('product_boms');
    }
};
