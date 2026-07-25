<?php

namespace Modules\OrderFulfillment\Console\Commands;

use Illuminate\Console\Command;

class InstallOrderFulfillmentSchema extends Command
{
    protected $signature = 'order-fulfillment:install-schema {--force}';
    public function handle(): int { return $this->call('migrate', ['--database' => 'order_fulfillment', '--path' => 'Modules/OrderFulfillment/database/manual-migrations', '--force' => (bool) $this->option('force')]); }
}
