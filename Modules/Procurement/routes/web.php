<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\Procurement\DashboardController;
use Modules\Procurement\Http\Controllers\Procurement\PurchaseOrderController;
use Modules\Procurement\Http\Controllers\Procurement\SupplierController;
use Modules\Procurement\Http\Controllers\Procurement\RequisitionController;
use Modules\Procurement\Http\Controllers\Procurement\DeliveryController;

/*
|--------------------------------------------------------------------------
| Web Routes — Nexora ERP Procurement Suite
|--------------------------------------------------------------------------
| Each module (Purchase Orders, Suppliers, Requisitions, Deliveries) has
| its own controller and its own Blade view under resources/views/pages.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Lightweight JSON counts for live (no-refresh) dashboard cards + sidebar badges.
Route::get('/live-stats', [DashboardController::class, 'liveStats'])->name('live-stats');

Route::post('/logout', function () {
    session()->forget(['employee_logged_in', 'employee_role', 'employee_id', 'employee_name', 'employee_email', 'employee_department', 'employee_position', 'employee_client_id']);
    return redirect()->route('login');
})->name('logout');

Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
    Route::get('/approved', [PurchaseOrderController::class, 'approved'])->name('approved');
    Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
    Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
    Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
});

Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
});

Route::prefix('requisitions')->name('requisitions.')->group(function () {
    Route::get('/', [RequisitionController::class, 'index'])->name('index');
    Route::post('/', [RequisitionController::class, 'store'])->name('store');
    Route::put('/{requisition}', [RequisitionController::class, 'update'])->name('update');
    Route::delete('/{requisition}', [RequisitionController::class, 'destroy'])->name('destroy');
});

Route::prefix('deliveries')->name('deliveries.')->group(function () {
    Route::get('/', [DeliveryController::class, 'index'])->name('index');
    Route::post('/', [DeliveryController::class, 'store'])->name('store');
    Route::put('/{delivery}', [DeliveryController::class, 'update'])->name('update');
    Route::delete('/{delivery}', [DeliveryController::class, 'destroy'])->name('destroy');
});
