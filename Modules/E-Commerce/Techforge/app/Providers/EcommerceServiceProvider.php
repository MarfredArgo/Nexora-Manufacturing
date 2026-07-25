<?php

namespace Modules\Ecommerce\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Ecommerce\Support\EcommerceClientContext;
use Modules\Ecommerce\Console\Commands\EnsureEcommerceClientColumns;
use Modules\Ecommerce\Console\Commands\AssignEcommerceCatalogToClient;
use Modules\Ecommerce\Console\Commands\BackfillEcommerceOrderLinks;
use Modules\Ecommerce\Console\Commands\InstallEcommerceSchema;
use Modules\Ecommerce\Services\ListingAvailabilityService;

class EcommerceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(EcommerceClientContext::class, fn (): EcommerceClientContext => new EcommerceClientContext());
        $this->app->scoped(ListingAvailabilityService::class, fn (): ListingAvailabilityService => new ListingAvailabilityService());
        $this->commands([
            EnsureEcommerceClientColumns::class,
            AssignEcommerceCatalogToClient::class,
            BackfillEcommerceOrderLinks::class,
            InstallEcommerceSchema::class,
        ]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'ecommerce');
        Route::middleware('web')->group(__DIR__.'/../../routes/web.php');
        // The standalone storefront uses <x-navbar>, <x-footer>, and related
        // anonymous components directly, so retain those component names after
        // moving its views into this module.
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/components');

    }
}
