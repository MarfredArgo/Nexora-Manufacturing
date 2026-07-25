<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\BomController;

Route::get('/', fn () => redirect()->route('manufacturing.dashboard'));

Route::middleware('manufacturing.access')->name('manufacturing.')->group(function (): void {
    Route::get('/dashboard', [ManufacturingController::class, 'index'])->name('dashboard');
    Route::middleware('manufacturing.bom')->group(function (): void {
        Route::get('/boms', [BomController::class, 'index'])->name('boms.index');
        Route::post('/boms', [BomController::class, 'store'])->name('boms.store');
        Route::delete('/boms/{bom}', [BomController::class, 'destroy'])->name('boms.destroy');
    });
    Route::post('/update-order', [ManufacturingController::class, 'updateOrder'])->name('update-order');
    Route::post('/cancel-order', [ManufacturingController::class, 'cancelOrder'])->name('cancel-order');
    Route::post('/update-qc', [ManufacturingController::class, 'updateQC'])->name('update-qc');
    Route::post('/update-rework', [ManufacturingController::class, 'updateRework'])->name('update-rework');
    Route::post('/add-rework-part', [ManufacturingController::class, 'addReworkPart'])->name('add-rework-part');
    Route::post('/update-rework-part', [ManufacturingController::class, 'updateReworkPart'])->name('update-rework-part');
    Route::post('/add-qc-note', [ManufacturingController::class, 'addQcNote'])->name('add-qc-note');
    Route::post('/send-to-inventory', [ManufacturingController::class, 'sendToInventory'])->name('send-to-inventory');
    Route::post('/receive-order', [ManufacturingController::class, 'receiveOrderFromEcommerce'])->name('receive-order');
    Route::post('/update-worker', [ManufacturingController::class, 'updateWorker'])->name('update-worker');
    Route::post('/delete-worker', [ManufacturingController::class, 'deleteWorker'])->name('delete-worker');
    Route::post('/worker', [ManufacturingController::class, 'addWorker'])->name('worker.store');
    Route::post('/assign-worker', [ManufacturingController::class, 'assignWorker'])->name('assign-worker');
});
