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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'employee_id')) {
                $table->foreignId('employee_id')->after('id');
            }

            if (!Schema::hasColumn('attendances', 'attendance_date')) {
                $table->date('attendance_date')->after('employee_id');
            }

            if (!Schema::hasColumn('attendances', 'time_in')) {
                $table->time('time_in')->nullable()->after('attendance_date');
            }

            if (!Schema::hasColumn('attendances', 'time_out')) {
                $table->time('time_out')->nullable()->after('time_in');
            }

            if (!Schema::hasColumn('attendances', 'status')) {
                $table->enum('status', ['Present', 'Late', 'Absent', 'Leave'])
                    ->nullable()
                    ->after('time_out');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('attendances', 'time_out')) {
                $table->dropColumn('time_out');
            }
            if (Schema::hasColumn('attendances', 'time_in')) {
                $table->dropColumn('time_in');
            }
            if (Schema::hasColumn('attendances', 'attendance_date')) {
                $table->dropColumn('attendance_date');
            }
            if (Schema::hasColumn('attendances', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
        });
    }
};
