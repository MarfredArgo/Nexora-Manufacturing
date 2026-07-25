<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = DB::connection('ecommerce');

        $connection->statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(255) NULL');
        $connection->statement('CREATE UNIQUE INDEX IF NOT EXISTS users_phone_unique ON users (phone)');
        $connection->statement('ALTER TABLE users ALTER COLUMN name DROP NOT NULL');
        $connection->statement('ALTER TABLE users ALTER COLUMN email DROP NOT NULL');
        $connection->statement('ALTER TABLE users ALTER COLUMN password DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('ecommerce')->statement('ALTER TABLE users DROP COLUMN IF EXISTS phone');
    }
};
