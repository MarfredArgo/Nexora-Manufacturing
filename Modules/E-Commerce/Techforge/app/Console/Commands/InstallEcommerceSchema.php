<?php

namespace Modules\Ecommerce\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallEcommerceSchema extends Command
{
    protected $signature = 'ecommerce:install-schema';

    protected $description = 'Install the dedicated E-Commerce schema without ITSM infrastructure tables';

    public function handle(): int
    {
        $schema = Schema::connection('ecommerce');

        // A copied migration ledger can claim the base user migration ran.
        if (! $schema->hasTable('users')) {
            $baseMigration = require base_path('Modules/E-Commerce/Techforge/database/migrations/0001_01_01_000000_create_users_table.php');
            $baseMigration->up();
        }

        return $this->call('migrate', [
            '--database' => 'ecommerce',
            '--path' => 'Modules/E-Commerce/Techforge/database/migrations',
            '--force' => true,
        ]);
    }
}
