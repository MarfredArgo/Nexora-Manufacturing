<?php

use App\Providers\AppServiceProvider;

// Composer rebuilds the module namespace during deployment. This fallback
// keeps local CLI commands usable before that rebuild.
spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Modules\\Inventory\\' => __DIR__.'/../Modules/Inventory/app/',
        'Modules\\Procurement\\' => __DIR__.'/../Modules/Procurement/app/',
        'Modules\\OrderFulfillment\\' => __DIR__.'/../Modules/OrderFulfillment/app/',
        'Modules\\Ecommerce\\' => __DIR__.'/../Modules/E-Commerce/Techforge/app/',
        'Modules\\Manufacturing\\' => __DIR__.'/../Modules/Manufacturing/app/',
        'Modules\\Finance\\' => __DIR__.'/../Modules/Finance/app/',
        'Modules\\BusinessIntelligence\\' => __DIR__.'/../Modules/BusinessIntelligence/app/',
    ];

    foreach ($prefixes as $prefix => $basePath) {
        if (! str_starts_with($class, $prefix)) {
            continue;
        }

        $path = $basePath.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';

        if (is_file($path)) {
            require_once $path;
        }

        return;
    }
});

require_once __DIR__.'/../Modules/Inventory/app/Providers/InventoryServiceProvider.php';
require_once __DIR__.'/../Modules/Procurement/app/Providers/ProcurementServiceProvider.php';
require_once __DIR__.'/../Modules/OrderFulfillment/app/Providers/OrderFulfillmentServiceProvider.php';
require_once __DIR__.'/../Modules/E-Commerce/Techforge/app/Providers/EcommerceServiceProvider.php';
require_once __DIR__.'/../Modules/Manufacturing/app/Providers/ManufacturingServiceProvider.php';
require_once __DIR__.'/../Modules/Finance/app/Providers/FinanceServiceProvider.php';
require_once __DIR__.'/../Modules/BusinessIntelligence/app/Providers/BusinessIntelligenceServiceProvider.php';

return [
    AppServiceProvider::class,
    \Modules\Inventory\Providers\InventoryServiceProvider::class,
    \Modules\Procurement\Providers\ProcurementServiceProvider::class,
    \Modules\OrderFulfillment\Providers\OrderFulfillmentServiceProvider::class,
    \Modules\Ecommerce\Providers\EcommerceServiceProvider::class,
    \Modules\Manufacturing\Providers\ManufacturingServiceProvider::class,
    \Modules\Finance\Providers\FinanceServiceProvider::class,
    \Modules\BusinessIntelligence\Providers\BusinessIntelligenceServiceProvider::class,
];
