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
        DB::connection('procurement')->statement('ALTER TABLE deliveries DROP CONSTRAINT IF EXISTS deliveries_status_check');
        DB::connection('procurement')->statement("ALTER TABLE deliveries ADD CONSTRAINT deliveries_status_check CHECK (status IN ('pending', 'scheduled', 'intransit', 'delivered', 'delayed', 'cancelled', 'completed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('procurement')->statement('ALTER TABLE deliveries DROP CONSTRAINT IF EXISTS deliveries_status_check');
        DB::connection('procurement')->statement("ALTER TABLE deliveries ADD CONSTRAINT deliveries_status_check CHECK (status IN ('pending', 'scheduled', 'intransit', 'delivered', 'delayed', 'cancelled'))");
    }
};
