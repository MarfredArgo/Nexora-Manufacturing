<?php

namespace Modules\BusinessIntelligence\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\BusinessIntelligence\Console\Commands\InstallBusinessIntelligenceSchema;
use Modules\BusinessIntelligence\Console\Commands\WarmDashboardCache;

class BusinessIntelligenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Load the module's AI config (Gemini/OpenAI provider settings) under
        // the `ai` namespace so config('ai.*') resolves. The file lives with
        // the provider classes rather than the app config directory.
        $this->mergeConfigFrom(__DIR__ . '/../Services/AI/Providers/ai.php', 'ai');

        $this->commands([
            InstallBusinessIntelligenceSchema::class,
            WarmDashboardCache::class,
        ]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'bi');

        Route::middleware('web')->prefix('bi')->group(__DIR__ . '/../../routes/web.php');

        // Keep the BI cache warm (and the remote Neon databases from cold-
        // starting) for the configured clients. Registered only in console so
        // it adds no per-request overhead; no-ops when the scheduler isn't
        // running or no clients are configured.
        if ($this->app->runningInConsole() && ! empty(config('nexora.bi_warm_clients', []))) {
            $this->app->booted(function (): void {
                $this->app->make(Schedule::class)
                    ->command('bi:warm-cache')
                    ->everyMinute()
                    ->withoutOverlapping();
            });
        }
    }
}
