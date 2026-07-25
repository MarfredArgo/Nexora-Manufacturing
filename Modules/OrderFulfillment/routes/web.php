<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Modules\OrderFulfillment\Http\Controllers\DashboardController;
use Modules\OrderFulfillment\Http\Controllers\OrderController;
use Modules\OrderFulfillment\Http\Controllers\PackingController;
use Modules\OrderFulfillment\Http\Controllers\ShippingController;
use Modules\OrderFulfillment\Http\Controllers\MaterialRequestController;
use Modules\OrderFulfillment\Http\Controllers\ReturnController;

// Protected order-fulfillment routes
Route::prefix('order-fulfillment')->name('order-fulfillment.')->middleware('order-fulfillment.access')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders/{id}/prepare', [OrderController::class, 'prepare'])->name('orders.prepare');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/packing', [PackingController::class, 'index'])->name('packing');
    Route::post('/packing/process/{id}', [PackingController::class, 'processOrder'])->name('packing.process');

    Route::get('/shipping', [ShippingController::class, 'index'])->name('shipping');
    Route::get('/shipping/{shipmentId}/drivers', [ShippingController::class, 'drivers'])->name('shipping.drivers');
    Route::post('/shipping/{shipmentId}/assign-driver', [ShippingController::class, 'assignDriver'])->name('shipping.assign-driver');
    Route::post('/shipping/{shipmentId}/cancel', [ShippingController::class, 'cancel'])->name('shipping.cancel');

    Route::post('/material-requests', [MaterialRequestController::class, 'store'])->name('material-requests.store');

Route::get('returns', [ReturnController::class, 'index'])->name('return');
Route::post('returns/{id}/accept', [ReturnController::class, 'accept'])->name('returns.accept');
Route::post('returns/{id}/status', [ReturnController::class, 'updateStatus'])->name('returns.status');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');


});