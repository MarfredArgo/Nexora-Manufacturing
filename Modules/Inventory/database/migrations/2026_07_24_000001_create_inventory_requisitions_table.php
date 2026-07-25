<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('inventory');

        if ($schema->hasTable('requisitions')) return;

        $schema->create('requisitions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('req_id')->unique();
            $table->string('part_name');
            $table->integer('quantity');
            $table->string('department')->nullable();
            $table->string('requested_by');
            $table->text('notes')->nullable();
            $table->string('destination')->nullable();
            $table->date('date_requested');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('inventory')->dropIfExists('requisitions');
    }
};
