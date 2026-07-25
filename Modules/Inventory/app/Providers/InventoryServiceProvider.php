<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Console\Commands\InstallInventorySchema;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([InstallInventorySchema::class]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'inventory');
        Route::middleware('web')
            ->prefix('inventory')
            ->group(__DIR__.'/../../routes/web.php');
    }
}
