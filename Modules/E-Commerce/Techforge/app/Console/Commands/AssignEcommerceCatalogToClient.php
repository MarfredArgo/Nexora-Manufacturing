<?php

namespace Modules\Ecommerce\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignEcommerceCatalogToClient extends Command
{
    protected $signature = 'ecommerce:assign-catalog {clientId : ITSM company/client ID that owns the existing catalog}';

    protected $description = 'Assigns only unassigned legacy ecommerce catalog rows to one client. Shopper and order data are never moved.';

    public function handle(): int
    {
        $clientId = (int) $this->argument('clientId');

        if ($clientId < 1) {
            $this->error('The client ID must be a positive integer.');

            return self::FAILURE;
        }

        $tables = [
            'accessories_headsets', 'accessories_keyboard_accessories', 'accessories_keyboards',
            'accessories_mice', 'accessories_monitors', 'accessories_mouse_pads',
            'accessories_speaker_systems', 'components_chasisfan', 'components_coolers',
            'components_cpus', 'components_gpus', 'components_motherboards', 'components_pc_cases',
            'components_power_supplies', 'components_rams', 'components_storages',
            'configurator_configs', 'gaminglaptops', 'prebuilt_configs',
        ];

        foreach ($tables as $table) {
            $count = DB::connection('ecommerce')->table($table)->whereNull('client_id')->update(['client_id' => $clientId]);
            $this->line("{$table}: {$count} rows assigned.");
        }

        $this->info("The legacy catalog is now assigned to client {$clientId}.");

        return self::SUCCESS;
    }
}
