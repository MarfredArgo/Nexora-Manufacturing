<?php

namespace Modules\BusinessIntelligence\Console\Commands;

use Illuminate\Console\Command;
use Modules\BusinessIntelligence\Http\Controllers\BusinessIntelligenceController;

class WarmDashboardCache extends Command
{
    protected $signature = 'bi:warm-cache {clients?* : Client ids to warm; defaults to config nexora.bi_warm_clients}';

    protected $description = 'Pre-compute and cache the BI dashboard/analytics data for the given clients so page loads read warm cache instead of hitting the ~15s live cross-database query path.';

    public function handle(BusinessIntelligenceController $controller): int
    {
        $clients = $this->argument('clients') ?: config('nexora.bi_warm_clients', []);

        if (empty($clients)) {
            $this->warn('No clients to warm. Pass ids (e.g. "php artisan bi:warm-cache 28") or set config nexora.bi_warm_clients.');

            return self::SUCCESS;
        }

        foreach ($clients as $clientId) {
            $started = microtime(true);
            $controller->warmCache((int) $clientId);
            $ms = round((microtime(true) - $started) * 1000);
            $this->info("Warmed BI cache for client {$clientId} in {$ms} ms.");
        }

        return self::SUCCESS;
    }
}
