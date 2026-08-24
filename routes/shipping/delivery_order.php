<?php

use App\Http\Controllers\DeliveryOrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'delivery_order', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:do.list']], function (){
        Route::get('/', [DeliveryOrderController::class, 'index'])->name('delivery_order');
        Route::get('/datatable', [DeliveryOrderController::class, 'datatable'])->name('delivery_order.datatable');
    });

    Route::group(['middleware' => ['check_permission:do.add']], function (){
        Route::get('/create', [DeliveryOrderController::class, 'create'])->name('delivery_order.create');
        Route::post('/store', [DeliveryOrderController::class, 'store'])->name('delivery_order.store');
    });

    Route::group(['middleware' => ['check_permission:do.show']], function (){
        Route::get('/show/{id}', [DeliveryOrderController::class, 'show'])->name('delivery_order.show');
    });

    Route::group(['middleware' => ['check_permission:do.edit']], function (){
        Route::get('/edit/{id}', [DeliveryOrderController::class, 'edit'])->name('delivery_order.edit');
        Route::post('/update', [DeliveryOrderController::class, 'update'])->name('delivery_order.update');
    });

    Route::group(['middleware' => ['check_permission:do.delete']], function (){
        Route::post('/void', [DeliveryOrderController::class, 'void'])->name('delivery_order.void');
    });

    Route::get('/select2', [DeliveryOrderController::class, 'select2'])->name('delivery_order.select2');
    Route::get('/item/select2', [DeliveryOrderController::class, 'select2Item'])->name('delivery_order.item.select2');
    Route::get('/container/select2', [DeliveryOrderController::class, 'select2Container'])->name('delivery_order.container.select2');
    Route::post('/item/get', [DeliveryOrderController::class, 'getItemData'])->name('delivery_order.item.get');
    Route::post('/container/get', [DeliveryOrderController::class, 'getContainerData'])->name('delivery_order.container.get');
});
