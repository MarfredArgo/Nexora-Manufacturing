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

        if ($schema->hasTable('storefront_layouts')) {
            return;
        }

        $schema->create('storefront_layouts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->unique();
            $table->json('draft_layout')->nullable();
            $table->json('published_layout')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('ecommerce')->dropIfExists('storefront_layouts');
    }
};
