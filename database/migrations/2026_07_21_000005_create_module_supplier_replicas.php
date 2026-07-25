<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        foreach (['inventory', 'manufacturing'] as $connection) {
            $schema = Schema::connection($connection);
            if ($schema->hasTable('integration_suppliers')) continue;
            $schema->create('integration_suppliers', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->unsignedBigInteger('source_supplier_id');
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('category')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
                $table->unique(['client_id', 'source_supplier_id']);
            });
        }
    }

    public function down(): void {}
};
