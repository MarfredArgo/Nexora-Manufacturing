<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\DashboardController;
use Modules\HR\Http\Controllers\EmployeeController;
use Modules\HR\Http\Controllers\DepartmentController;
use Modules\HR\Http\Controllers\AttendanceController;
use Modules\HR\Http\Controllers\EmployeeOnboardingController;
use Modules\HR\Http\Controllers\ReportsAnalyticsController;
use Modules\HR\Http\Controllers\DeliveryDriverController;
use Modules\HR\Models\Attendance;

Route::get('/', function () {
    return redirect()->route('hr.dashboard');
});

Route::middleware('hr.access')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/employee-dashboard', [DashboardController::class, 'employeeIndex'])
        ->name('employee.dashboard');

    Route::post('/logout', function () {
        session()->forget(['employee_logged_in', 'employee_role', 'employee_department', 'employee_id', 'employee_code']);

        return redirect()->route('login');
    })->name('logout');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/drivers', [DeliveryDriverController::class, 'index'])->name('drivers.index');
    Route::post('/drivers', [DeliveryDriverController::class, 'store'])->name('drivers.store');
    Route::put('/drivers/{driver}', [DeliveryDriverController::class, 'update'])->name('drivers.update');

    Route::get('/departments', [DepartmentController::class, 'index'])
        ->name('departments.index');
    Route::get('/departments/{slug}', [DepartmentController::class, 'show'])
        ->name('departments.show');

    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/step1', [EmployeeOnboardingController::class, 'step1'])->name('step1');
        Route::post('/step1', [EmployeeOnboardingController::class, 'storeStep1'])->name('storeStep1');

        Route::get('/step2', [EmployeeOnboardingController::class, 'step2'])->name('step2');
        Route::post('/step2', [EmployeeOnboardingController::class, 'storeStep2'])->name('storeStep2');

        Route::get('/step3', [EmployeeOnboardingController::class, 'step3'])->name('step3');
        Route::post('/step3', [EmployeeOnboardingController::class, 'storeStep3'])->name('storeStep3');

        Route::get('/step4', [EmployeeOnboardingController::class, 'step4'])->name('step4');
        Route::post('/step4', [EmployeeOnboardingController::class, 'storeStep4'])->name('storeStep4');

        Route::get('/success', [EmployeeOnboardingController::class, 'success'])->name('success');
    });

    Route::get('/reports-analytics/attendance-overview', [ReportsAnalyticsController::class, 'index'])
        ->name('reports-analytics.attendance-overview');

    Route::get('/reports-analytics/employee-attendance/{employee}', [ReportsAnalyticsController::class, 'employeeAttendance'])
        ->name('reports-analytics.employee-attendance');

    Route::get('/reports-analytics/leave', [ReportsAnalyticsController::class, 'leave'])
        ->name('reports-analytics.leave');

    Route::get('/attendance/today-count', function () {
        return response()->json([
            'count' => Attendance::whereDate('attendance_date', today())
                ->whereNotNull('time_in')
                ->whereNull('time_out')
                ->count()
        ]);
    });
});

Route::get('/clockinout', function () {
    return view('clockinout.index');
})->name('clockinout');

Route::post('/clock-in', [AttendanceController::class, 'clockIn'])
    ->name('clockinout.index');
