<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('custombuilt_configs', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
                        $table->string('tier'); // Core, Advanced, Extreme, Apex
            $table->foreignId('intel_cpu_id')->constrained('cpus');
            $table->foreignId('amd_cpu_id')->constrained('cpus');
            $table->foreignId('intel_motherboard_id')->constrained('motherboards');
            $table->foreignId('amd_motherboard_id')->constrained('motherboards');
            $table->foreignId('intel_ram_id')->nullable()->constrained('rams');
            $table->foreignId('amd_ram_id')->nullable()->constrained('rams');
            $table->foreignId('gpu_id')->constrained('gpus');
            $table->foreignId('storage_id')->constrained('storages');
            $table->foreignId('power_supply_id')->constrained('power_supplies');
            $table->foreignId('pc_case_id')->nullable()->constrained('pc_cases');
            $table->foreignId('cooler_id')->nullable()->constrained('components_coolers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custombuilt_configs');
    }
};