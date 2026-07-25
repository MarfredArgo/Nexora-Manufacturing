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
        if (Schema::connection('manufacturing')->hasTable('workers')) {
            return;
        }

        Schema::connection('manufacturing')->create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('role', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable()->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('workers');
    }
};
