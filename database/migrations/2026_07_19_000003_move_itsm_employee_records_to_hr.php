<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $hrSchema = Schema::connection('hr');

        if (! $hrSchema->hasTable('employees')) {
            $hrSchema->create('employees', function (Blueprint $table): void {
                $table->id();
                $table->string('employee_id')->nullable();
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('company_email')->nullable();
                $table->string('temporary_password')->nullable();
                $table->string('phone')->nullable();
                $table->string('department')->nullable();
                $table->string('position')->nullable();
                $table->date('hire_date')->nullable();
                $table->string('work_schedule')->nullable();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->string('approval_status')->default('Active');
                $table->timestamps();
            });
        } else {
            $hrSchema->table('employees', function (Blueprint $table) use ($hrSchema): void {
                if (! $hrSchema->hasColumn('employees', 'employee_id')) {
                    $table->string('employee_id')->nullable();
                }
                if (! $hrSchema->hasColumn('employees', 'company_email')) {
                    $table->string('company_email')->nullable();
                }
                if (! $hrSchema->hasColumn('employees', 'temporary_password')) {
                    $table->string('temporary_password')->nullable();
                }
                if (! $hrSchema->hasColumn('employees', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable()->index();
                }
                if (! $hrSchema->hasColumn('employees', 'approval_status')) {
                    $table->string('approval_status')->default('Active');
                }
            });
        }

        $companies = DB::table('companies')->get();

        // Earlier versions accidentally stored HR-manager profiles on the
        // generic module connection. Move the known company manager profile
        // to the dedicated HR database before removing ITSM employee tables.
        if (Schema::connection('modules')->hasTable('employees')) {
            foreach ($companies as $company) {
                if (! $company->hr_employee_id) {
                    continue;
                }

                $employee = DB::connection('modules')->table('employees')->find($company->hr_employee_id);
                if (! $employee) {
                    continue;
                }

                $existing = DB::connection('hr')->table('employees')
                    ->where('client_id', $company->id)
                    ->where('email', $employee->email)
                    ->first();

                $values = [
                    'employee_id' => $employee->employee_id ?? null,
                    'first_name' => $employee->first_name ?? 'HR',
                    'last_name' => $employee->last_name ?? 'Manager',
                    'email' => $employee->email,
                    'company_email' => $employee->company_email ?? null,
                    'temporary_password' => $employee->temporary_password ?? null,
                    'phone' => $employee->phone ?? null,
                    'department' => $employee->department ?? 'Human Resources',
                    'position' => $employee->position ?? 'HR Manager',
                    'hire_date' => $employee->hire_date ?? null,
                    'work_schedule' => $employee->work_schedule ?? null,
                    'client_id' => $company->id,
                    'approval_status' => empty($employee->company_email) ? 'Pending' : 'Active',
                    'updated_at' => now(),
                ];

                if ($existing) {
                    DB::connection('hr')->table('employees')->where('id', $existing->id)->update($values);
                } else {
                    DB::connection('hr')->table('employees')->insert($values + ['created_at' => now()]);
                }
            }
        }

        foreach ($companies->whereNotNull('employee_table_name') as $company) {
            $tableName = $company->employee_table_name;

            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach (DB::table($tableName)->orderBy('id')->get() as $employee) {
                // A company administrator is an ITSM identity, not an HR employee.
                if ($employee->department === 'Administration') {
                    continue;
                }

                $name = preg_split('/\s+/', trim($employee->name), 2);
                $values = [
                    'employee_id' => $employee->employee_code,
                    'first_name' => $name[0] ?: 'Employee',
                    'last_name' => $name[1] ?? null,
                    'email' => $employee->email,
                    'company_email' => $employee->username,
                    'department' => $employee->department,
                    'client_id' => $company->id,
                    'approval_status' => $employee->status ?? 'Active',
                    'updated_at' => now(),
                ];

                $existing = DB::connection('hr')->table('employees')
                    ->where('client_id', $company->id)
                    ->where(function ($query) use ($employee): void {
                        $query->where('employee_id', $employee->employee_code)
                            ->orWhere('email', $employee->email);
                    })
                    ->first();

                if ($existing) {
                    DB::connection('hr')->table('employees')->where('id', $existing->id)->update($values);
                } else {
                    DB::connection('hr')->table('employees')->insert($values + ['created_at' => now()]);
                }
            }

            Schema::drop($tableName);
        }

        DB::table('companies')->update(['employee_table_name' => null]);
    }

    public function down(): void
    {
        // Employee data is now owned by HR and must not be copied back to ITSM.
    }
};
