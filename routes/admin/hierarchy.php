<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HierarchyController;

Route::group(['prefix' => 'level', 'middleware' => ['auth', 'auth.session']], function (){
    Route::group(['middleware' => ['check_permission:level.list']], function (){
        Route::get('/', [HierarchyController::class, 'index'])->name('level');
        Route::get('/datatable', [HierarchyController::class, 'datatable'])->name('level.datatable');
    });

    Route::group(['middleware' => ['check_permission:level.add']], function (){
        Route::get('/create', [HierarchyController::class, 'add'])->name('level.create');
        Route::post('/store', [HierarchyController::class, 'store'])->name('level.store');
    });

    Route::group(['middleware' => ['check_permission:level.edit']], function (){
        Route::get('/edit/{id}', [HierarchyController::class, 'edit'])->name('level.edit');
        Route::post('/update', [HierarchyController::class, 'update'])->name('level.update');
    });

    Route::get('/select2', [HierarchyController::class, 'select2'])->name('level.select2');
});
