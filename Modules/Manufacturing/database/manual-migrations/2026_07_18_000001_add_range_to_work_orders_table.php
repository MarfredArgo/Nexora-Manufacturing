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
        Schema::connection('manufacturing')->table('work_orders', function (Blueprint $table) {
            if (!Schema::connection('manufacturing')->hasColumn('work_orders', 'range')) {
                $table->string('range', 20)->nullable()->after('assigned');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('manufacturing')->table('work_orders', function (Blueprint $table) {
            $table->dropColumn('range');
        });
    }
};
