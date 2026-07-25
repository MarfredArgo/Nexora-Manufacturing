<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        // Older versions of the HR module used the application's default
        // connection. Move every legacy profile to HR before removing that
        // table from the ITSM database.
        if (! Schema::hasTable('employees')) {
            return;
        }

        $companiesByLegacyEmployeeId = DB::table('companies')
            ->whereNotNull('hr_employee_id')
            ->get()
            ->keyBy('hr_employee_id');

        foreach (DB::table('employees')->orderBy('id')->get() as $employee) {
            $company = $companiesByLegacyEmployeeId->get($employee->id);
            $companyId = $company?->id;

            $existing = DB::connection('hr')->table('employees')
                ->where('email', $employee->email)
                ->first();

            $values = [
                'employee_id' => $employee->employee_id ?? null,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name ?? null,
                'email' => $employee->email,
                'company_email' => $employee->company_email ?? null,
                'temporary_password' => $employee->temporary_password ?? null,
                'phone' => $employee->phone ?? null,
                'department' => $employee->department ?? null,
                'position' => $employee->position ?? null,
                'hire_date' => $employee->hire_date ?? null,
                'work_schedule' => $employee->work_schedule ?? null,
                'client_id' => $companyId,
                'approval_status' => empty($employee->company_email) ? 'Pending' : 'Active',
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::connection('hr')->table('employees')->where('id', $existing->id)->update($values);
                $hrEmployeeId = $existing->id;
            } else {
                $hrEmployeeId = DB::connection('hr')->table('employees')->insertGetId($values + ['created_at' => now()]);
            }

            if ($company) {
                DB::table('companies')->where('id', $company->id)->update([
                    'hr_employee_id' => $hrEmployeeId,
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::drop('employees');
    }

    public function down(): void
    {
        // HR remains the employee-data owner; do not recreate an ITSM copy.
    }
};
