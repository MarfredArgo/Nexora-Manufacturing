<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'manufacturing';

    public function up(): void
    {
        if (Schema::connection('manufacturing')->hasTable('requisitions')) {
            return;
        }

        Schema::connection('manufacturing')->create('requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('req_id', 20)->unique();
            $table->string('part_name', 150);
            $table->integer('quantity')->default(1);
            $table->string('department', 100)->default('Manufacturing');
            $table->string('requested_by', 100);
            $table->string('priority', 20)->default('Medium');
            $table->string('wo_id', 20)->nullable();
            $table->text('notes')->nullable();
            $table->date('date_requested')->default(DB::raw('CURRENT_DATE'));
            $table->string('status', 50)->default('Pending');
            $table->timestamp('created_at')->nullable()->default(DB::raw('now()'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('now()'));
        });
    }

    public function down(): void
    {
        Schema::connection('manufacturing')->dropIfExists('requisitions');
    }
};
