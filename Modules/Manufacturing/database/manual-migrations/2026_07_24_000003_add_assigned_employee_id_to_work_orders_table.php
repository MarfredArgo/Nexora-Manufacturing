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
        $schema = Schema::connection('manufacturing');

        if (! $schema->hasColumn('work_orders', 'assigned_employee_id')) {
            $schema->table('work_orders', function (Blueprint $table): void {
                $table->unsignedBigInteger('assigned_employee_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('manufacturing');

        if ($schema->hasColumn('work_orders', 'assigned_employee_id')) {
            $schema->table('work_orders', function (Blueprint $table): void {
                $table->dropColumn('assigned_employee_id');
            });
        }
    }
};
