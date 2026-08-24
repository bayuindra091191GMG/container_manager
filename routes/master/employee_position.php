<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeePositionController;

Route::group(['prefix' => 'position', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:job_position.list']], function (){
        Route::get('/', [EmployeePositionController::class, 'index'])->name('position');
        Route::get('/datatable', [EmployeePositionController::class, 'datatable'])->name('position.datatable');
    });

    Route::group(['middleware' => ['check_permission:job_position.add']], function (){
        Route::get('/create', [EmployeePositionController::class, 'create'])->name('position.create');
        Route::post('/store', [EmployeePositionController::class, 'store'])->name('position.store');
    });

    Route::group(['middleware' => ['check_permission:job_position.show']], function (){
        Route::get('/show/{id}', [EmployeePositionController::class, 'show'])->name('position.show');
    });

    Route::group(['middleware' => ['check_permission:job_position.list']], function (){
        Route::get('/edit/{id}', [EmployeePositionController::class, 'edit'])->name('position.edit');
        Route::post('/update', [EmployeePositionController::class, 'update'])->name('position.update');
    });

    Route::get('/select2', [EmployeePositionController::class, 'select2'])->name('position.select2');
});
