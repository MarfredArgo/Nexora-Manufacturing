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
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'employee_table_name')) {
                $table->string('employee_table_name')->nullable()->after('admin_user_id');
            }
        });

        DB::table('companies')->orderBy('id')->get()->each(function ($company): void {
            $tableName = 'company_employees_' . $company->id;

            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table): void {
                    $table->id();
                    $table->string('employee_code')->nullable();
                    $table->string('username')->nullable();
                    $table->string('name');
                    $table->string('email')->nullable();
                    $table->string('department')->nullable();
                    $table->string('status')->default('Active');
                    $table->timestamps();
                });
            }

            DB::table('companies')->where('id', $company->id)->update([
                'employee_table_name' => $tableName,
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('companies')->whereNotNull('employee_table_name')->pluck('employee_table_name')->each(function (string $tableName): void {
            Schema::dropIfExists($tableName);
        });

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'employee_table_name')) {
                $table->dropColumn('employee_table_name');
            }
        });
    }
};
