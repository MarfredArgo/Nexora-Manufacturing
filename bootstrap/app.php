<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(__DIR__.'/../Modules/E-Commerce/Techforge/routes/web.php');
            
            Route::middleware('web')
                ->group(__DIR__.'/../routes/web.php');
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->appendToGroup('web', \App\Http\Middleware\AuditModuleAction::class);
        $middleware->alias([
            'hr.access' => \Modules\HR\Http\Middleware\EmployeeAuth::class,
            'inventory.access' => \Modules\Inventory\Http\Middleware\InventoryAccess::class,
            'procurement.access' => \Modules\Procurement\Http\Middleware\ProcurementAccess::class,
            'order-fulfillment.access' => \Modules\OrderFulfillment\Http\Middleware\OrderFulfillmentAccess::class,
            'ecommerce.client' => \Modules\Ecommerce\Http\Middleware\ResolveStorefrontClient::class,
            'ecommerce.admin' => \Modules\Ecommerce\Http\Middleware\ResolveEcommerceAdminClient::class,
            'manufacturing.access' => \Modules\Manufacturing\Http\Middleware\ManufacturingAccess::class,
            'manufacturing.bom' => \Modules\Manufacturing\Http\Middleware\ManufacturingBomAccess::class,
        'finance.access' => \Modules\Finance\Http\Middleware\FinanceAccess::class,
        'bi.access' => \Modules\BusinessIntelligence\Http\Middleware\BusinessIntelligenceAccess::class,
            'root.admin' => \App\Http\Middleware\EnsureRootAdmin::class,
            'client.admin' => \App\Http\Middleware\EnsureClientAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
