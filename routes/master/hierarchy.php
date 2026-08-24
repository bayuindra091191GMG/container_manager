<?php

use App\Http\Controllers\HierarchyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'hierarchy', 'middleware' => ['auth']], function () {
    Route::group(['middleware' => ['permission:hierarchy.list']], function () {
        Route::get('/', [HierarchyController::class, 'index'])->name('hierarchy');
        Route::get('/datatable', [HierarchyController::class, 'datatable'])->name('hierarchy.datatable');
    });

    Route::group(['middleware' => ['permission:hierarchy.add']], function () {
        Route::get('/create', [HierarchyController::class, 'create'])->name('hierarchy.create');
        Route::post('/store', [HierarchyController::class, 'store'])->name('hierarchy.store');
    });

    Route::group(['middleware' => ['permission:hierarchy.show']], function () {
        Route::get('/show/{id}', [HierarchyController::class, 'show'])->name('hierarchy.show');
    });

    Route::group(['middleware' => ['permission:hierarchy.list']], function () {
        Route::get('/edit/{id}', [HierarchyController::class, 'edit'])->name('hierarchy.edit');
        Route::post('/update', [HierarchyController::class, 'update'])->name('hierarchy.update');
    });

    Route::get('/select2', [HierarchyController::class, 'select2'])->name('hierarchy.select2');
});
