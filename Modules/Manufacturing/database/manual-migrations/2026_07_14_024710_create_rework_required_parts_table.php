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
        if (Schema::connection('manufacturing')->hasTable('rework_required_parts')) {
            return;
        }

        Schema::connection('manufacturing')->create('rework_required_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rework_id', 20);
            $table->string('name', 150);
            $table->string('status', 50)->default('Sourcing');
            $table->string('eta', 50)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('rework_required_parts');
    }
};
