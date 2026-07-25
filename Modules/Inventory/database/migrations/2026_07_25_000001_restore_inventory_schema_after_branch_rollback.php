<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        // Neon point-in-time restores can rewind the Inventory branch without
        // rewinding its migration ledger. The base migration is idempotent.
        if (Schema::connection('inventory')->hasTable('categories')) {
            return;
        }

        $baseMigration = require __DIR__.'/2026_07_20_000001_create_inventory_module_tables.php';
        $baseMigration->up();
    }

    public function down(): void
    {
        // Never remove client-owned Inventory data during rollback.
    }
};
