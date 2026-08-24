<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

Route::group(['prefix' => 'employee', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:employee.list']], function (){
        Route::get('/', [EmployeeController::class, 'index'])->name('employee');
        Route::get('/datatable', [EmployeeController::class, 'datatable'])->name('employee.datatable');
    });

    Route::group(['middleware' => ['check_permission:employee.add']], function (){
        Route::get('/create', [EmployeeController::class, 'create'])->name('employee.create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('employee.store');
    });

    Route::group(['middleware' => ['check_permission:employee.show']], function (){
        Route::get('/show/{id}', [EmployeeController::class, 'show'])->name('employee.show');
    });

    Route::group(['middleware' => ['check_permission:employee.list']], function (){
        Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::post('/update', [EmployeeController::class, 'update'])->name('employee.update');
    });

    Route::get('/select2', [EmployeeController::class, 'select2'])->name('employee.select2');
});
