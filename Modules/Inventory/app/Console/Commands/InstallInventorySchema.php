<?php

namespace Modules\Inventory\Console\Commands;

use Illuminate\Console\Command;

class InstallInventorySchema extends Command
{
    protected $signature = 'inventory:install-schema';

    protected $description = 'Install Inventory tables on the dedicated Inventory database only';

    public function handle(): int
    {
        return $this->call('migrate', [
            '--database' => 'inventory',
            '--path' => 'Modules/Inventory/database/migrations',
            '--force' => true,
        ]);
    }
}
