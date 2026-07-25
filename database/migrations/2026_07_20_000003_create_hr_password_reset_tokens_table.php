<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('hr');

        if (! $schema->hasTable('password_reset_tokens')) {
            $schema->create('password_reset_tokens', function (Blueprint $table): void {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Reset tokens are security records; do not remove them on rollback.
    }
};
