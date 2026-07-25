<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('ecommerce');

        if ($schema->hasTable('storefront_listings')) {
            return;
        }

        $schema->create('storefront_listings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->unsignedBigInteger('bom_id')->index();
            $table->string('sku', 100);
            $table->string('name', 160);
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('image_url')->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
            $table->unique(['client_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::connection('ecommerce')->dropIfExists('storefront_listings');
    }
};
