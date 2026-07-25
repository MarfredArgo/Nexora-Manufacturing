<?php

namespace Modules\HR\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class HRServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any module services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'hr');
        View::addLocation(__DIR__ . '/../../resources/views');

        // Keep HR routes and names isolated from ITSM while making the module
        // available from the same application at /hr.
        Route::middleware('web')
            ->prefix('hr')
            ->as('hr.')
            ->group(__DIR__ . '/../../routes/web.php');
    }

    /**
     * Register any module services.
     */
    public function register(): void
    {
        // 
    }
}
