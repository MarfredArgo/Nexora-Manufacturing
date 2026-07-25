<?php

namespace Modules\Manufacturing\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignManufacturingDataToClient extends Command
{
    protected $signature = 'manufacturing:assign-legacy-data {clientId : ITSM client ID that owns the existing Manufacturing records}';

    protected $description = 'Assigns only unassigned legacy Manufacturing records to one client.';

    public function handle(): int
    {
        $clientId = (int) $this->argument('clientId');

        if ($clientId < 1) {
            $this->error('The client ID must be a positive integer.');

            return self::FAILURE;
        }

        $tables = [
            'work_orders', 'work_order_parts', 'workers', 'qc_sessions', 'qc_results',
            'rework_orders', 'rework_failed_checks', 'rework_required_parts', 'requisitions',
        ];

        foreach ($tables as $table) {
            $count = DB::connection('manufacturing')->table($table)->whereNull('client_id')->update(['client_id' => $clientId]);
            $this->line("{$table}: {$count} rows assigned.");
        }

        $this->info("Manufacturing data is now assigned to client {$clientId}.");

        return self::SUCCESS;
    }
}
