<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * Run the migrations.
     *
     * Why this migration exists: the "in-transit" -> "intransit" rename was
     * done by editing the SQL text inside the OLD migration file
     * (2026_07_20_000004_update_deliveries_status_check.php). Laravel only
     * runs a migration once and remembers it by filename in the
     * `migrations` table, so editing that file's contents did nothing to
     * a database where it had already run â€” the live `deliveries_status_check`
     * constraint (and any existing rows) were still using the old
     * 'in-transit' value. Meanwhile every controller/JS file was updated to
     * send 'intransit' (no dash), so every new delivery insert/update was
     * rejected by the database with a CHECK constraint violation â€” that is
     * the "PO logging errors" bug. This migration brings the live database
     * in sync with the current 'intransit' convention.
     */
    public function up(): void
    {
        // Drop the OLD constraint FIRST â€” it still only allows 'in-transit',
        // so updating rows to 'intransit' while it's still active gets
        // rejected by the same check constraint we're trying to fix.
        DB::connection('procurement')->statement('ALTER TABLE deliveries DROP CONSTRAINT IF EXISTS deliveries_status_check');

        // Now it's safe to normalize any existing rows still holding the
        // old value.
        DB::connection('procurement')->table('deliveries')
            ->where('status', 'in-transit')
            ->update(['status' => 'intransit']);

        // Recreate the constraint so it matches what the app actually
        // sends now.
        DB::connection('procurement')->statement("ALTER TABLE deliveries ADD CONSTRAINT deliveries_status_check CHECK (status IN ('pending', 'scheduled', 'intransit', 'delivered', 'delayed', 'cancelled', 'completed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('procurement')->statement('ALTER TABLE deliveries DROP CONSTRAINT IF EXISTS deliveries_status_check');

        DB::connection('procurement')->table('deliveries')
            ->where('status', 'intransit')
            ->update(['status' => 'in-transit']);

        DB::connection('procurement')->statement("ALTER TABLE deliveries ADD CONSTRAINT deliveries_status_check CHECK (status IN ('pending', 'scheduled', 'in-transit', 'delivered', 'delayed', 'cancelled', 'completed'))");
    }
};
