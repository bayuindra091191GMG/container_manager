<?php

use App\Http\Controllers\DistributorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'distributor', 'middleware' => ['auth']], function () {
    Route::group(['middleware' => ['permission:distributor.list']], function () {
        Route::get('/', [DistributorController::class, 'index'])->name('distributor');
        Route::get('/datatable', [DistributorController::class, 'datatable'])->name('distributor.datatable');
    });

    Route::group(['middleware' => ['permission:distributor.add']], function () {
        Route::get('/create', [DistributorController::class, 'create'])->name('distributor.create');
        Route::post('/store', [DistributorController::class, 'store'])->name('distributor.store');
    });

    Route::group(['middleware' => ['permission:distributor.list']], function () {
        Route::get('/edit/{id}', [DistributorController::class, 'edit'])->name('distributor.edit');
        Route::post('/update/{id}', [DistributorController::class, 'update'])->name('distributor.update');
    });

    Route::group(['middleware' => ['permission:distributor.delete']], function () {
        Route::post('/delete', [DistributorController::class, 'delete'])->name('distributor.delete');
    });

    Route::get('/select2', [DistributorController::class, 'select2'])->name('distributor.select2');
});
