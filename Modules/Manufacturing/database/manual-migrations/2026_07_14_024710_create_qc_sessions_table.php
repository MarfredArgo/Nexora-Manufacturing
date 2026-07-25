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
        if (Schema::connection('manufacturing')->hasTable('qc_sessions')) {
            return;
        }

        Schema::connection('manufacturing')->create('qc_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('wo_id', 20);
            $table->string('build_type', 50)->nullable();
            $table->string('tech', 100)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('qc_sessions');
    }
};
