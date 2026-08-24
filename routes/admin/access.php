<?php

use App\Http\Controllers\MenuAccessController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'access', 'middleware' => ['auth', 'permission:menu_access']], function () {
    Route::get('/', [MenuAccessController::class, 'index'])->name('access');
    Route::get('/permission', [MenuAccessController::class, 'getPermission'])->name('access.permission');
    Route::post('/permission', [MenuAccessController::class, 'setPermission'])->name('access.permission.set');
});
