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
        if (!Schema::connection('manufacturing')->hasColumn('work_orders', 'due_date')) {
            Schema::connection('manufacturing')->table('work_orders', function (Blueprint $table) {
                $table->date('due_date')->nullable()->after('due');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('manufacturing')->table('work_orders', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
