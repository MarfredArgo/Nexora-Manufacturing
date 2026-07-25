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
        Schema::rename('cpus', 'components_cpus');
        Schema::rename('gpus', 'components_gpus');
        Schema::rename('motherboards', 'components_motherboards');
        Schema::rename('rams', 'components_rams');
        Schema::rename('storages', 'components_storages');
        Schema::rename('power_supplies', 'components_power_supplies');
        Schema::rename('pc_cases', 'components_pc_cases');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('components_cpus', 'cpus');
        Schema::rename('components_gpus', 'gpus');
        Schema::rename('components_motherboards', 'motherboards');
        Schema::rename('components_rams', 'rams');
        Schema::rename('components_storages', 'storages');
        Schema::rename('components_power_supplies', 'power_supplies');
        Schema::rename('components_pc_cases', 'pc_cases');
    }
};
