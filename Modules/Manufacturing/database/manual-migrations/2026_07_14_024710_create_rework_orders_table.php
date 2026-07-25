<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'manufacturing';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::connection('manufacturing')->hasTable('rework_orders')) {
            return;
        }

        Schema::connection('manufacturing')->create('rework_orders', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('wo_id', 20);
            $table->string('build_name', 150)->nullable();
            $table->string('assigned_tech', 100)->nullable();
            $table->string('raised_by', 100)->nullable();
            $table->string('raised_date', 50)->nullable();
            $table->string('status', 50)->default('In Rework');
            $table->string('priority', 20)->default('Medium');
            $table->text('notes')->nullable();
            $table->boolean('escalated_to_procurement')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('rework_orders');
    }
};
