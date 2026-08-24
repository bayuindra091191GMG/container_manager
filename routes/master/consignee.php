<?php

use App\Http\Controllers\ConsigneeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'consignee', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:consignee.list']], function (){
        Route::get('/', [ConsigneeController::class, 'index'])->name('consignee');
        Route::get('/datatable', [ConsigneeController::class, 'datatable'])->name('consignee.datatable');
    });

    Route::group(['middleware' => ['check_permission:consignee.add']], function (){
        Route::get('/create', [ConsigneeController::class, 'create'])->name('consignee.create');
        Route::post('/store', [ConsigneeController::class, 'store'])->name('consignee.store');
    });

    Route::group(['middleware' => ['check_permission:consignee.show']], function (){
        Route::get('/show/{id}', [ConsigneeController::class, 'show'])->name('consignee.show');
    });

    Route::group(['middleware' => ['check_permission:consignee.list']], function (){
        Route::get('/edit/{id}', [ConsigneeController::class, 'edit'])->name('consignee.edit');
        Route::post('/update', [ConsigneeController::class, 'update'])->name('consignee.update');
    });

    Route::group(['middleware' => ['check_permission:consignee.delete']], function (){
        Route::post('/delete', [ConsigneeController::class, 'delete'])->name('consignee.delete');
    });

    Route::get('/select2', [ConsigneeController::class, 'select'])->name('consignee.select2');
    Route::get('/province_select2', [ConsigneeController::class, 'provinceSelect'])->name('province.select2');
});
