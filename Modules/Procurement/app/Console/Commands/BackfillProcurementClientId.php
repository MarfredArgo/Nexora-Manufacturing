<?php

namespace Modules\Procurement\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Assigns a client_id to legacy Procurement rows that were created before the
 * multi-tenant migration and therefore have client_id = NULL. Those rows are
 * hidden by the BelongsToClient global scope (which is deliberate — it must
 * never leak un-owned records across tenants), so they show up as "empty"
 * tables in the UI until they are claimed by a specific client.
 *
 * This command is intentionally conservative:
 *   • it only ever updates rows WHERE client_id IS NULL (never re-tenants a row
 *     that already belongs to someone);
 *   • it requires the target client id to be passed explicitly;
 *   • --dry-run previews the counts without writing anything;
 *   • run with no argument to get a report of NULL rows per table first.
 */
class BackfillProcurementClientId extends Command
{
    protected $signature = 'procurement:backfill-client-id
        {client? : The client_id to assign to legacy rows that have no client}
        {--dry-run : Show what would change without writing anything}';

    protected $description = 'Assign a client_id to legacy Procurement rows that have NULL client_id (so they are no longer hidden by tenant scoping).';

    private array $tables = [
        'suppliers', 'supplier_products', 'requisitions', 'requisition_items',
        'purchase_orders', 'purchase_order_items', 'deliveries',
    ];

    public function handle(): int
    {
        $schema = Schema::connection('procurement');
        $db = DB::connection('procurement');

        $applicable = array_filter($this->tables, fn ($t) => $schema->hasTable($t) && $schema->hasColumn($t, 'client_id'));

        if (empty($applicable)) {
            $this->error('No Procurement tables with a client_id column were found on the "procurement" connection. Run procurement:ensure-client-columns first.');

            return self::FAILURE;
        }

        // Report mode — no client id given.
        if ($this->argument('client') === null) {
            $this->info('Legacy rows with no client (client_id IS NULL):');
            $grandTotal = 0;
            foreach ($applicable as $table) {
                $null = $db->table($table)->whereNull('client_id')->count();
                $grandTotal += $null;
                $this->line(sprintf('  %-24s %d', $table, $null));
            }

            // Show which client ids already own data, to help pick a target.
            $known = collect();
            foreach (['purchase_orders', 'suppliers', 'deliveries'] as $table) {
                if (in_array($table, $applicable, true)) {
                    $known = $known->merge(
                        $db->table($table)->whereNotNull('client_id')->distinct()->pluck('client_id')
                    );
                }
            }
            $known = $known->unique()->sort()->values();
            $this->line('');
            $this->line('Existing client ids in the data: '.($known->isEmpty() ? '(none)' : $known->implode(', ')));

            if ($grandTotal === 0) {
                $this->info('Nothing to backfill — no NULL-client rows found.');

                return self::SUCCESS;
            }

            $this->warn('Re-run with the target client id, e.g.:');
            $this->warn('  php artisan procurement:backfill-client-id <clientId> --dry-run   (preview)');
            $this->warn('  php artisan procurement:backfill-client-id <clientId>             (apply)');

            return self::SUCCESS;
        }

        $client = (int) $this->argument('client');
        if ($client <= 0) {
            $this->error('The client id must be a positive integer.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $total = 0;

        foreach ($applicable as $table) {
            $count = $db->table($table)->whereNull('client_id')->count();
            if ($count === 0) {
                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] would set client_id={$client} on {$count} {$table} row(s).");
            } else {
                $db->table($table)->whereNull('client_id')->update(['client_id' => $client]);
                $this->info("Set client_id={$client} on {$count} {$table} row(s).");
            }

            $total += $count;
        }

        $this->info(($dryRun ? '[dry-run] ' : '')."Done. {$total} legacy row(s) ".($dryRun ? 'would be' : 'were')." assigned to client {$client}.");

        return self::SUCCESS;
    }
}
