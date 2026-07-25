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
        Schema::create('prebuilt_configs', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->foreignId('cpu_id')->constrained('cpus');
            $table->foreignId('gpu_id')->constrained('gpus');
            $table->foreignId('motherboard_id')->constrained('motherboards');
            $table->foreignId('ram_id')->constrained('rams');
            $table->foreignId('storage_id')->constrained('storages');
            $table->foreignId('power_supply_id')->constrained('power_supplies');
            $table->foreignId('pc_case_id')->nullable()->constrained('pc_cases');
            $table->foreignId('cooler_id')->nullable()->constrained('components_coolers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prebuilt_configs');
    }
};
