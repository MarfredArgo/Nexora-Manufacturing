<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('hr');

        if (! $schema->hasTable('employees')) {
            $schema->create('employees', function (Blueprint $table): void {
                $table->id();
                $table->string('employee_id')->nullable();
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name');
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
                $table->string('email');
                $table->string('company_email')->nullable();
                $table->string('temporary_password')->nullable();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->unique(['client_id', 'email'], 'employees_client_id_email_unique');
                $table->unique(['client_id', 'company_email'], 'employees_client_id_company_email_unique');
                $table->string('approval_status')->default('Active');
                $table->string('birth_certificate')->nullable();
                $table->string('curriculum_vitae')->nullable();
                $table->string('valid_id')->nullable();
                $table->string('medical_certificate')->nullable();
                $table->string('signature')->nullable();
                $table->timestamps();
            });

            return;
        }

        $schema->table('employees', function (Blueprint $table) use ($schema): void {
            if (! $schema->hasColumn('employees', 'employee_id')) {
                $table->string('employee_id')->nullable();
            }

            if (! $schema->hasColumn('employees', 'first_name')) {
                $table->string('first_name')->nullable();
            }

            if (! $schema->hasColumn('employees', 'middle_name')) {
                $table->string('middle_name')->nullable();
            }

            if (! $schema->hasColumn('employees', 'last_name')) {
                $table->string('last_name')->nullable();
            }

            if (! $schema->hasColumn('employees', 'suffix')) {
                $table->string('suffix')->nullable();
            }

            if (! $schema->hasColumn('employees', 'gender')) {
                $table->string('gender')->nullable();
            }

            if (! $schema->hasColumn('employees', 'marital_status')) {
                $table->string('marital_status')->nullable();
            }

            if (! $schema->hasColumn('employees', 'nationality')) {
                $table->string('nationality')->nullable();
            }

            if (! $schema->hasColumn('employees', 'profile_picture')) {
                $table->string('profile_picture')->nullable();
            }

            if (! $schema->hasColumn('employees', 'address')) {
                $table->text('address')->nullable();
            }

            if (! $schema->hasColumn('employees', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (! $schema->hasColumn('employees', 'department')) {
                $table->string('department')->nullable();
            }

            if (! $schema->hasColumn('employees', 'position')) {
                $table->string('position')->nullable();
            }

            if (! $schema->hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }

            if (! $schema->hasColumn('employees', 'work_schedule')) {
                $table->string('work_schedule')->nullable();
            }

            if (! $schema->hasColumn('employees', 'email')) {
                $table->string('email')->nullable();
            }

            if (! $schema->hasColumn('employees', 'company_email')) {
                $table->string('company_email')->nullable();
            }

            if (! $schema->hasColumn('employees', 'temporary_password')) {
                $table->string('temporary_password')->nullable();
            }

            if (! $schema->hasColumn('employees', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            }

            if (! $schema->hasColumn('employees', 'approval_status')) {
                $table->string('approval_status')->default('Active');
            }

            if (! $schema->hasColumn('employees', 'birth_certificate')) {
                $table->string('birth_certificate')->nullable();
            }

            if (! $schema->hasColumn('employees', 'curriculum_vitae')) {
                $table->string('curriculum_vitae')->nullable();
            }

            if (! $schema->hasColumn('employees', 'valid_id')) {
                $table->string('valid_id')->nullable();
            }

            if (! $schema->hasColumn('employees', 'medical_certificate')) {
                $table->string('medical_certificate')->nullable();
            }

            if (! $schema->hasColumn('employees', 'signature')) {
                $table->string('signature')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Intentionally non-destructive because this table is owned by the HR module.
    }
};
