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
        Schema::create('gaminglaptops', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('processor');
            $table->string('gpu');
            $table->string('ram');
            $table->string('storage');
            $table->string('display')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaminglaptops');
    }
};
