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
        if (Schema::connection('manufacturing')->hasTable('qc_results')) {
            return;
        }

        Schema::connection('manufacturing')->create('qc_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('session_id');
            $table->string('check_id', 10);
            $table->decimal('value')->nullable();
            $table->string('verdict', 10)->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('qc_results');
    }
};
