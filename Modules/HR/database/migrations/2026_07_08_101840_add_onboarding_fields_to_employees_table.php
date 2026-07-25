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
    Schema::table('employees', function (Blueprint $table) {

        $table->string('middle_name')->nullable();
        $table->string('suffix')->nullable();
        $table->string('gender')->nullable();
        $table->string('marital_status')->nullable();
        $table->string('nationality')->nullable();
        $table->text('address')->nullable();

        $table->date('hire_date')->nullable();
        $table->string('work_schedule')->nullable();

        $table->string('birth_certificate')->nullable();
        $table->string('curriculum_vitae')->nullable();
        $table->string('valid_id')->nullable();
        $table->string('medical_certificate')->nullable();


        $table->string('company_email')->unique()->nullable();
        $table->string('temporary_password')->nullable();

       

    });
}


public function down(): void
{
    Schema::table('employees', function (Blueprint $table) {

        $table->dropColumn([
            'middle_name',
            'suffix',
            'gender',
            'marital_status',
            'nationality',
            'address',
            'hire_date',
            'work_schedule',
            'birth_certificate',
            'curriculum_vitae',
            'valid_id',
            'medical_certificate',
            'employee_id',
            'company_email',
            'temporary_password',
         
        ]);

    });
}
    };
