<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignFinanceDataToClient extends Command
{
    protected $signature = 'finance:assign-legacy-data {clientId : ITSM client ID that owns existing Finance records}';
    protected $description = 'Assigns only unassigned legacy Finance records to one Nexora client.';

    public function handle(): int
    {
        $clientId = (int) $this->argument('clientId');

        foreach (['accounts', 'invoice', 'expenses', 'sales'] as $table) {
            $count = DB::connection('finance')->table($table)->whereNull('nexora_client_id')->update(['nexora_client_id' => $clientId]);
            $this->line("Assigned {$count} {$table} records.");
        }

        $this->info("Finance records are now assigned to client {$clientId}.");
        return self::SUCCESS;
    }
}
