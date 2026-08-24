<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;

Route::group(['prefix' => 'branch', 'middleware' => ['auth']], function (){
    Route::group(['middleware' => ['check_permission:branch.list']], function (){
        Route::get('/', [BranchController::class, 'index'])->name('branch');
        Route::get('/datatable', [BranchController::class, 'datatable'])->name('branch.datatable');
    });

    Route::group(['middleware' => ['check_permission:branch.add']], function (){
        Route::get('/create', [BranchController::class, 'create'])->name('branch.create');
        Route::post('/store', [BranchController::class, 'store'])->name('branch.store');
    });

    Route::group(['middleware' => ['check_permission:branch.show']], function (){
        Route::get('/show/{id}', [BranchController::class, 'show'])->name('branch.show');
    });

    Route::group(['middleware' => ['check_permission:branch.list']], function (){
        Route::get('/edit/{id}', [BranchController::class, 'edit'])->name('branch.edit');
        Route::post('/update', [BranchController::class, 'update'])->name('branch.update');
    });

    Route::get('/select2', [BranchController::class, 'select2'])->name('branch.select2');
});
