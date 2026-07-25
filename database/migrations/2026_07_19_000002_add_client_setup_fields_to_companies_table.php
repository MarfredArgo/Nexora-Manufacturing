<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            if (! Schema::hasColumn('companies', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('employee_table_name');
            }

            if (! Schema::hasColumn('companies', 'hr_employee_id')) {
                $table->unsignedBigInteger('hr_employee_id')->nullable()->after('logo_path');
            }

            if (! Schema::hasColumn('companies', 'setup_completed_at')) {
                $table->timestamp('setup_completed_at')->nullable()->after('hr_employee_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            if (Schema::hasColumn('companies', 'setup_completed_at')) {
                $table->dropColumn('setup_completed_at');
            }

            if (Schema::hasColumn('companies', 'hr_employee_id')) {
                $table->dropColumn('hr_employee_id');
            }

            if (Schema::hasColumn('companies', 'logo_path')) {
                $table->dropColumn('logo_path');
            }
        });
    }
};
