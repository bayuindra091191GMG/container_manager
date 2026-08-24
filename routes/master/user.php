<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'role:superadmin']], function (){
    Route::get('/', [UserController::class, 'index'])->name('user');
    Route::get('/datatable', [UserController::class, 'datatable'])->name('user.datatable');
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/show/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/update', [UserController::class, 'update'])->name('user.update');
});
