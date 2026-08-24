<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['auth', 'auth.session'])->group(function (){
    Route::get('/password', [AuthController::class, 'changePassword'] )->name('password');
    Route::post('/password/update', [AuthController::class, 'updatePassword'] )->name('password.update');
});
