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
        if (Schema::connection('manufacturing')->hasTable('work_orders')) {
            return;
        }

        Schema::connection('manufacturing')->create('work_orders', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('name', 150);
            $table->string('specs')->nullable();
            $table->string('status', 50)->default('Pending');
            $table->string('due', 50)->nullable();
            $table->string('source', 150)->nullable();
            $table->string('assigned', 100)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('work_orders');
    }
};
