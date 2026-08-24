<?php

use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'warehouse', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:warehouse.list']], function (){
        Route::get('/', [WarehouseController::class, 'index'])->name('warehouse');
        Route::get('/datatable', [WarehouseController::class, 'datatable'])->name('warehouse.datatable');
    });

    Route::group(['middleware' => ['check_permission:warehouse.add']], function (){
        Route::get('/create', [WarehouseController::class, 'create'])->name('warehouse.create');
        Route::post('/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    });

    Route::group(['middleware' => ['check_permission:warehouse.show']], function (){
        Route::get('/show/{id}', [WarehouseController::class, 'show'])->name('warehouse.show');
    });

    Route::group(['middleware' => ['check_permission:warehouse.list']], function (){
        Route::get('/edit/{id}', [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::post('/update', [WarehouseController::class, 'update'])->name('warehouse.update');
    });

    Route::group(['middleware' => ['check_permission:warehouse.delete']], function (){
        Route::post('/delete', [WarehouseController::class, 'delete'])->name('warehouse.delete');
    });

    Route::get('/select2', [WarehouseController::class, 'select2'])->name('warehouse.select2');
});
