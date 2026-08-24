<?php

use App\Http\Controllers\ContainerManagementController;
use App\Http\Controllers\WarehouseManagementController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'trx_warehouse', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:warehouse_management.list']], function (){
        Route::get('/', [WarehouseManagementController::class, 'index'])->name('trx_warehouse');
        Route::get('/datatable', [WarehouseManagementController::class, 'datatable'])->name('trx_warehouse.datatable');
    });

    // Route::group(['middleware' => ['check_permission:warehouse.add']], function (){
    //     Route::get('/create', [WarehouseController::class, 'create'])->name('warehouse.create');
    //     Route::post('/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    // });

    Route::group(['middleware' => ['check_permission:warehouse_management.show']], function (){
        Route::get('/show/{id}', [WarehouseManagementController::class, 'show'])->name('trx_warehouse.show');
        Route::get('/download', [WarehouseManagementController::class, 'downloadAll'])->name('trx_warehouse.download');
    });

    Route::group(['middleware' => ['check_permission:warehouse_management.list']], function (){
        Route::get('/edit/{id}', [WarehouseManagementController::class, 'edit'])->name('trx_warehouse.edit');
        Route::post('/update', [WarehouseManagementController::class, 'update'])->name('trx_warehouse.update');
    });
});

Route::group(['prefix' => 'container', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:warehouse_management.list']], function (){
        Route::get('/', [WarehouseManagementController::class, 'indexContainer'])->name('container');
        Route::get('/datatable', [WarehouseManagementController::class, 'datatableContainer'])->name('container.datatable');
        Route::get('/export_list', [ContainerManagementController::class, 'exportList'])->name('container.list.export');
        Route::get('/operation', [ContainerManagementController::class, 'operation'])->name('container.operation');
        Route::post('/operation/update', [ContainerManagementController::class, 'updateOperation'])->name('container.operation.update');
    });
});

Route::group(['prefix' => 'wh_operation', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:warehouse_management.list']], function (){
        Route::get('/control', [WarehouseManagementController::class, 'management'])->name('container.management');
        Route::post('/control/update', [WarehouseManagementController::class, 'updateWarehouseData'])->name('container.management.update');
    });
});
