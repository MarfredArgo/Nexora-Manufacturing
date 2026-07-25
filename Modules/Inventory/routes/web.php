<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\DashboardController;
use Modules\Inventory\Http\Controllers\ItemCatalogController;
use Modules\Inventory\Http\Controllers\StockAdjustmentController;
use Modules\Inventory\Http\Controllers\StockLevelController;
use Modules\Inventory\Http\Controllers\StockMovementController;
use Modules\Inventory\Http\Controllers\RequestController;
use Modules\Inventory\Http\Controllers\StockReceivingController;
use Modules\Inventory\Http\Controllers\StockTransferController;
use Modules\Inventory\Http\Controllers\WarehouseController;

Route::get('/', fn () => redirect()->route('inventory.index'));

Route::post('/logout', function () {
    session()->forget([
        'employee_logged_in', 'employee_role', 'employee_id', 'employee_name',
        'employee_email', 'employee_department', 'employee_client_id',
    ]);

    return redirect()->route('login');
})->name('inventory.logout');

Route::middleware('inventory.access')->name('inventory.')->group(function (): void {
    Route::get('/index', [DashboardController::class, 'index'])->name('index');
    Route::get('/index/trend-data', [DashboardController::class, 'trendData'])->name('index.trend-data');

    Route::get('/item-catalog', [ItemCatalogController::class, 'index'])->name('item-catalog');
    Route::post('/item-catalog', [ItemCatalogController::class, 'store'])->name('item-catalog.store');
    Route::delete('/item-catalog/{item}', [ItemCatalogController::class, 'destroy'])->name('item-catalog.destroy');
    Route::post('/item-catalog/{item}/verify-password', [ItemCatalogController::class, 'verifyPassword'])->name('item-catalog.verify-password');
    Route::post('/item-catalog/packing-material', [ItemCatalogController::class, 'storePackingMaterial'])->name('item-catalog.packing.store');
    Route::delete('/item-catalog/packing-material/{id}', [ItemCatalogController::class, 'destroyPackingMaterial'])->name('item-catalog.packing.destroy');

    Route::get('/requests', [RequestController::class, 'index'])->name('requests');
    Route::get('/requests/restock-items', [RequestController::class, 'restockItems'])->name('requests.restock-items');
    Route::get('/requests/replacement-items', [RequestController::class, 'replacementItems'])->name('requests.replacement-items');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');

    Route::get('/stock-movement', [StockMovementController::class, 'index'])->name('stock-movement');
    Route::patch('/stock-levels/{stockLevel}', [StockLevelController::class, 'update'])->name('stock-levels.update');
    Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])->name('stock-adjustments');
    Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('stock-adjustments.store');
    Route::patch('/stock-adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('stock-adjustments.approve');
    Route::patch('/stock-adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('stock-adjustments.reject');
    Route::patch('/stock-adjustments/{adjustment}/cancel', [StockAdjustmentController::class, 'cancel'])->name('stock-adjustments.cancel');
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse');
    Route::post('/warehouse', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::patch('/warehouse/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.update');
    Route::delete('/warehouse/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');
    Route::patch('/warehouse/{warehouse}/toggle', [WarehouseController::class, 'toggle'])->name('warehouse.toggle');
    Route::get('/stock-receiving', [StockReceivingController::class, 'index'])->name('stock-receiving');
    Route::post('/stock-receiving/{delivery}/approve', [StockReceivingController::class, 'approve'])->name('stock-receiving.approve');
    Route::post('/stock-receiving/{delivery}/reject', [StockReceivingController::class, 'reject'])->name('stock-receiving.reject');
    Route::get('/stock-transfers', [StockTransferController::class, 'index'])->name('stock-transfers');
    Route::post('/stock-transfers', [StockTransferController::class, 'store'])->name('stock-transfers.store');
    Route::patch('/stock-transfers/{transfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
    Route::patch('/stock-transfers/{transfer}/reject', [StockTransferController::class, 'reject'])->name('stock-transfers.reject');
    Route::patch('/stock-transfers/{transfer}/cancel', [StockTransferController::class, 'cancel'])->name('stock-transfers.cancel');
});

// Compatibility for an already-compiled standalone Inventory dashboard view.
// New code uses inventory.index.trend-data; this alias prevents stale Blade
// caches from failing during a rolling deployment.
Route::middleware('inventory.access')
    ->get('/legacy/index/trend-data', [DashboardController::class, 'trendData'])
    ->name('index.trend-data');
