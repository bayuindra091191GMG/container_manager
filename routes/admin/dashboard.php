<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::prefix('dashboard')->group(function(){
    Route::get('/demurrage/get', [DashboardController::class, 'getTotalDemurrage'])->name('admin.dashboard.get_total_demurrage');
    Route::get('/do/get', [DashboardController::class, 'getLastDo'])->name('admin.dashboard.get_last_do');
    Route::get('/container/type/get', [DashboardController::class, 'getContainerType'])->name('admin.dashboard.get_container_type');
    Route::get('/container/overdue/get', [DashboardController::class, 'getContainerOverdue'])->name('admin.dashboard.get_container_overdue');
    Route::get('/SI/status/get', [DashboardController::class, 'getSIStatus'])->name('admin.dashboard.get_si_status');
    Route::get('/SI/container_status/get', [DashboardController::class, 'getSIContainerStatus'])->name('admin.dashboard.get_si_container_status');
});
