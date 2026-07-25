<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'manufacturing';

    public function up(): void
    {
        if (Schema::connection('manufacturing')->hasColumn('rework_orders', 'escalated_to_procurement')
            && !Schema::connection('manufacturing')->hasColumn('rework_orders', 'escalated_to_inventory')) {
            Schema::connection('manufacturing')->table('rework_orders', function (Blueprint $table) {
                $table->renameColumn('escalated_to_procurement', 'escalated_to_inventory');
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('manufacturing')->hasColumn('rework_orders', 'escalated_to_inventory')
            && !Schema::connection('manufacturing')->hasColumn('rework_orders', 'escalated_to_procurement')) {
            Schema::connection('manufacturing')->table('rework_orders', function (Blueprint $table) {
                $table->renameColumn('escalated_to_inventory', 'escalated_to_procurement');
            });
        }
    }
};
