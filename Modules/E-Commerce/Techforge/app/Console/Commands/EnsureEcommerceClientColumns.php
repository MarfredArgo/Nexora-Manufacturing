<?php

namespace Modules\Ecommerce\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureEcommerceClientColumns extends Command
{
    protected $signature = 'ecommerce:ensure-client-columns';

    protected $description = 'Adds client_id indexes to the dedicated ecommerce database without touching ITSM.';

    public function handle(): int
    {
        $schema = Schema::connection('ecommerce');
        $tables = [
            'accessories_headsets', 'accessories_keyboard_accessories', 'accessories_keyboards',
            'accessories_mice', 'accessories_monitors', 'accessories_mouse_pads',
            'accessories_speaker_systems', 'addresses', 'cart_items', 'carts',
            'components_chasisfan', 'components_coolers', 'components_cpus', 'components_gpus',
            'components_motherboards', 'components_pc_cases', 'components_power_supplies',
            'components_rams', 'components_storages', 'configurator_configs', 'gaminglaptops',
            'order_items', 'orders', 'payment_methods', 'prebuilt_configs', 'users',
        ];

        foreach ($tables as $table) {
            if (! $schema->hasTable($table) || $schema->hasColumn($table, 'client_id')) {
                continue;
            }

            $schema->table($table, function (Blueprint $blueprint): void {
                $blueprint->unsignedBigInteger('client_id')->nullable()->index();
            });

            $this->line("Added client_id to {$table}.");
        }

        $this->info('Ecommerce client columns are ready. Existing rows remain unassigned until explicitly migrated.');

        return self::SUCCESS;
    }
}
