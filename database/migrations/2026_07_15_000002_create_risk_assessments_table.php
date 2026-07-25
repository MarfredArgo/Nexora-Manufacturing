<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('level')->default('Medium');
            $table->string('owner')->nullable();
            $table->string('status')->default('Monitoring');
            $table->date('review_date')->nullable();
            $table->text('mitigation_plan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
    }
};
