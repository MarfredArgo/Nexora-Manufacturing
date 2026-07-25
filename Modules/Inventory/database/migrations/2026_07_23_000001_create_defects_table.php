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

        if ($schema->hasTable('defects')) {
            return;
        }

        $schema->create('defects', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('part_name', 255);
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('Open');
            $table->string('source', 100)->nullable();
            $table->string('source_id', 50)->nullable();
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('inventory')->dropIfExists('defects');
    }
};
