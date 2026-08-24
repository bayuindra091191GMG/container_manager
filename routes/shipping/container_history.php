<?php

use App\Http\Controllers\ContainerHistoryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cont_history', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:container_history.list']], function (){
        Route::get('/', [ContainerHistoryController::class, 'index'])->name('container_history');
        Route::get('/datatable', [ContainerHistoryController::class, 'datatable'])->name('container_history.datatable');
        Route::get('/export_list', [ContainerHistoryController::class, 'exportList'])->name('container_history.list.export');
    });
});
