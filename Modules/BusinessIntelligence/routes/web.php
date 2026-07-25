<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessIntelligence\Http\Controllers\BusinessIntelligenceController;

Route::get('/', fn () => redirect()->route('bi.dashboard'));

Route::middleware('bi.access')->name('bi.')->group(function (): void {
    Route::get('/dashboard', [BusinessIntelligenceController::class, 'dashboard'])->name('dashboard');
    Route::get('/department-analytics', [BusinessIntelligenceController::class, 'departmentAnalytics'])->name('department-analytics');
    Route::get('/live-monitor', [BusinessIntelligenceController::class, 'liveMonitor'])->name('live-monitor');
    Route::get('/ai-insights', [BusinessIntelligenceController::class, 'aiInsights'])->name('ai-insights');
    Route::post('/api/ai/chat', [BusinessIntelligenceController::class, 'aiChat'])->name('ai.chat');

    Route::get('/api/live-feed', [BusinessIntelligenceController::class, 'liveFeed'])->name('live-feed');
    Route::get('/api/department/{department}', [BusinessIntelligenceController::class, 'departmentData'])
        ->whereIn('department', ['finance', 'inventory', 'procurement', 'manufacturing', 'fulfillment', 'ecommerce'])
        ->name('department-data');
    Route::get('/api/sales-forecast', [BusinessIntelligenceController::class, 'salesForecast'])->name('sales-forecast');
});
