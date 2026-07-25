<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * The live `categories` table was created by an earlier schema revision
     * that made `name` GLOBALLY unique (constraint `categories_name_unique`).
     * The module design intends categories to be unique *per client*
     * (`unique(['client_id', 'name'])`) so separate tenants can each have
     * common names like "CPU". The drift meant one client claiming "CPU"
     * blocked every other client from ever creating it â€” which crashed the
     * stock-receiving auto-category step with a duplicate-key violation.
     *
     * This migration corrects the live schema to match the intended design.
     */
    public function up(): void
    {
        $conn = DB::connection('inventory');

        // Only the PostgreSQL tenant databases carry this drift.
        if ($conn->getDriverName() !== 'pgsql') {
            return;
        }

        // A point-in-time restore can leave this branch without the base
        // Inventory schema while Laravel's migration ledger remains ahead.
        if (! Schema::connection('inventory')->hasTable('categories')) {
            return;
        }

        // Remove the incorrect global-unique-on-name. Handle it whether it
        // exists as a table constraint or as a bare unique index.
        $conn->statement('ALTER TABLE categories DROP CONSTRAINT IF EXISTS categories_name_unique');
        $conn->statement('DROP INDEX IF EXISTS categories_name_unique');

        // Add the intended per-tenant composite unique, if not already present.
        $exists = $conn->selectOne("
            SELECT 1
            FROM pg_constraint c
            JOIN pg_class t ON t.oid = c.conrelid
            WHERE t.relname = 'categories'
              AND c.conname = 'categories_client_id_name_unique'
        ");

        if (! $exists) {
            $conn->statement('ALTER TABLE categories ADD CONSTRAINT categories_client_id_name_unique UNIQUE (client_id, name)');
        }
    }

    public function down(): void
    {
        $conn = DB::connection('inventory');

        if ($conn->getDriverName() !== 'pgsql') {
            return;
        }

        if (! Schema::connection('inventory')->hasTable('categories')) {
            return;
        }

        // Only remove what this migration added. Never restore the broken
        // global-unique constraint, and never destroy client-owned data.
        $conn->statement('ALTER TABLE categories DROP CONSTRAINT IF EXISTS categories_client_id_name_unique');
    }
};
