<?php

namespace Modules\Finance\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Finance\Console\Commands\InstallFinanceSchema;
use Modules\Finance\Console\Commands\AssignFinanceDataToClient;

class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([InstallFinanceSchema::class, AssignFinanceDataToClient::class]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'finance');

        Route::middleware('web')->prefix('finance')->group(__DIR__.'/../../routes/web.php');
    }
}
