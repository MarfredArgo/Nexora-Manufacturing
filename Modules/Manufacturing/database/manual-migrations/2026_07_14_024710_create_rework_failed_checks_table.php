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
        if (Schema::connection('manufacturing')->hasTable('rework_failed_checks')) {
            return;
        }

        Schema::connection('manufacturing')->create('rework_failed_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rework_id', 20);
            $table->string('check_id', 10)->nullable();
            $table->string('check_name', 150)->nullable();
            $table->string('verdict', 10)->nullable();
            $table->string('result', 50)->nullable();
            $table->string('target', 50)->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('rework_failed_checks');
    }
};
