<?php

namespace Modules\OrderFulfillment\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OrderFulfillmentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'order-fulfillment');
        $this->commands([
            \Modules\OrderFulfillment\Console\Commands\InstallOrderFulfillmentSchema::class,
            \Modules\OrderFulfillment\Console\Commands\EnsureOrderFulfillmentClientColumns::class,
        ]);
        Route::middleware('web')->group(__DIR__.'/../../routes/web.php');
    }
}
