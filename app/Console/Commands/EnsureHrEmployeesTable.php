<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureHrEmployeesTable extends Command
{
    protected $signature = 'hr:ensure-employees-table';

    protected $description = 'Create or repair the HR employees table on the dedicated HR database connection';

    public function handle(): int
    {
        $schema = Schema::connection('hr');

        if (! $schema->hasTable('employees')) {
            $schema->create('employees', function (Blueprint $table): void {
                $table->id();
                $table->string('employee_id')->nullable();
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('suffix')->nullable();
                $table->string('gender')->nullable();
                $table->string('marital_status')->nullable();
                $table->string('nationality')->nullable();
                $table->string('profile_picture')->nullable();
                $table->text('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('department')->nullable();
                $table->string('position')->nullable();
                $table->date('hire_date')->nullable();
                $table->string('work_schedule')->nullable();
                $table->string('email')->nullable()->unique();
                $table->string('company_email')->nullable()->unique();
                $table->string('temporary_password')->nullable();
                $table->boolean('must_change_password')->default(false);
                $table->string('birth_certificate')->nullable();
                $table->string('curriculum_vitae')->nullable();
                $table->string('valid_id')->nullable();
                $table->string('medical_certificate')->nullable();
                $table->string('signature')->nullable();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->string('approval_status')->default('Active');
                $table->timestamps();
            });

        }

        $hasLegacyEmployeeClientColumn = $schema->hasColumn('employees', 'itsm_company_id');
        $hasEmployeeClientColumn = $schema->hasColumn('employees', 'client_id');

        $schema->table('employees', function (Blueprint $table) use ($schema, $hasLegacyEmployeeClientColumn, $hasEmployeeClientColumn): void {
            if (! $schema->hasColumn('employees', 'company_email')) {
                $table->string('company_email')->nullable()->unique();
            }
            if (! $schema->hasColumn('employees', 'temporary_password')) {
                $table->string('temporary_password')->nullable();
            }
            if (! $schema->hasColumn('employees', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false);
            }
            if (! $hasEmployeeClientColumn) {
                if ($hasLegacyEmployeeClientColumn) {
                    $table->renameColumn('itsm_company_id', 'client_id');
                } else {
                    $table->unsignedBigInteger('client_id')->nullable()->index();
                }
            }
            if (! $schema->hasColumn('employees', 'approval_status')) {
                $table->string('approval_status')->default('Active');
            }
        });

        if ($hasLegacyEmployeeClientColumn && $hasEmployeeClientColumn) {
            DB::connection('hr')->table('employees')
                ->whereNull('client_id')
                ->whereNotNull('itsm_company_id')
                ->update(['client_id' => DB::raw('itsm_company_id')]);
        }

        if (! $schema->hasTable('departments')) {
            $schema->create('departments', function (Blueprint $table): void {
                $table->id();
                $table->string('department_name')->unique();
                $table->string('department_code')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('attendances')) {
            $schema->create('attendances', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->date('attendance_date');
                $table->time('time_in')->nullable();
                $table->string('time_in_image')->nullable();
                $table->time('time_out')->nullable();
                $table->string('time_out_image')->nullable();
                $table->string('status')->nullable();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->timestamps();
                $table->unique(['employee_id', 'attendance_date']);
            });
        }

        $hasLegacyAttendanceClientColumn = $schema->hasColumn('attendances', 'itsm_company_id');
        $hasAttendanceClientColumn = $schema->hasColumn('attendances', 'client_id');

        if (! $hasAttendanceClientColumn) {
            $schema->table('attendances', function (Blueprint $table) use ($hasLegacyAttendanceClientColumn): void {
                if ($hasLegacyAttendanceClientColumn) {
                    $table->renameColumn('itsm_company_id', 'client_id');
                } else {
                    $table->unsignedBigInteger('client_id')->nullable()->index();
                }
            });
        }

        if ($hasLegacyAttendanceClientColumn && $hasAttendanceClientColumn) {
            DB::connection('hr')->table('attendances')
                ->whereNull('client_id')
                ->whereNotNull('itsm_company_id')
                ->update(['client_id' => DB::raw('itsm_company_id')]);
        }

        $this->info('Verified the HR employees table.');

        return self::SUCCESS;
    }
}
