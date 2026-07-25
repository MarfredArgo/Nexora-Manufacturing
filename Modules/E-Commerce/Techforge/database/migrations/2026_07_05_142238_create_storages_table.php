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
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('type'); // NVMe Gen4, NVMe Gen5, SATA SSD, HDD
            $table->integer('capacity'); // in GB
            $table->string('cache')->nullable();
            $table->string('form_factor')->nullable(); // e.g. M.2 2280
            $table->string('interface')->nullable(); // e.g. PCIe 4.0 x4
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
};
