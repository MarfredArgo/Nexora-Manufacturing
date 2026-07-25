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
        Schema::create('components_coolers', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('fan_rpm')->nullable();
            $table->string('noise_level')->nullable();
            $table->string('color')->nullable();
            $table->string('radiator_size')->nullable(); // e.g. "240mm", "360mm", "N/A" for air coolers
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components_coolers');
    }
};
