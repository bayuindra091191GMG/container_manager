<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'auth.session']], function () {
    Route::group(['middleware' => ['permission:user.list']], function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/datatable', [UserController::class, 'datatable'])->name('user.datatable');
    });

    Route::group(['middleware' => ['permission:user.add']], function () {
        Route::get('/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
    });

    Route::group(['middleware' => ['permission:user.edit']], function () {
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/update', [UserController::class, 'update'])->name('user.update');
    });
});
