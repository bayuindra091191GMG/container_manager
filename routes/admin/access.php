<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuAccessController;

Route::group(['prefix' => 'access', 'middleware' => ['auth', 'check_permission:menu_access']], function (){
    Route::get('/', [MenuAccessController::class, 'index'])->name('access');
    Route::get('/permission', [MenuAccessController::class, 'getPermission'])->name('access.permission');
    Route::post('/permission', [MenuAccessController::class, 'setPermission'])->name('access.permission.set');
});
