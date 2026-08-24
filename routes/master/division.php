<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DivisionController;

Route::group(['prefix' => 'division', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:division.list']], function (){
        Route::get('/', [DivisionController::class, 'index'])->name('division');
        Route::get('/datatable', [DivisionController::class, 'datatable'])->name('division.datatable');
    });

    Route::group(['middleware' => ['check_permission:division.add']], function (){
        Route::get('/create', [DivisionController::class, 'create'])->name('division.create');
        Route::post('/store', [DivisionController::class, 'store'])->name('division.store');
    });

    Route::group(['middleware' => ['check_permission:division.show']], function (){
        Route::get('/show/{id}', [DivisionController::class, 'show'])->name('division.show');
    });

    Route::group(['middleware' => ['check_permission:division.list']], function (){
        Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('division.edit');
        Route::post('/update', [DivisionController::class, 'update'])->name('division.update');
    });

    Route::get('/select2', [DivisionController::class, 'select2'])->name('division.select2');
});
