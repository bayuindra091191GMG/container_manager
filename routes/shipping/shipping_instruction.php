<?php

use App\Http\Controllers\ShippingInstructionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'shipping_instruction', 'middleware' => ['auth']], function () {
    Route::group(['middleware' => ['check_permission:si.list']], function () {
        Route::get('/', [ShippingInstructionController::class, 'index'])->name('shipping_instruction');
        Route::get('/datatable', [ShippingInstructionController::class, 'datatable'])->name('shipping_instruction.datatable');
    });

    Route::group(['middleware' => ['check_permission:si.add']], function () {
        Route::get('/create', [ShippingInstructionController::class, 'create'])->name('shipping_instruction.create');
        Route::post('/store', [ShippingInstructionController::class, 'store'])->name('shipping_instruction.store');
    });

    Route::group(['middleware' => ['check_permission:si.show']], function () {
        Route::get('/show/{id}', [ShippingInstructionController::class, 'show'])->name('shipping_instruction.show');
        Route::get('/operation/{id}/{status}', [ShippingInstructionController::class, 'operation'])->name('shipping_instruction.operation');
    });

    Route::group(['middleware' => ['check_permission:si.edit-si']], function () {
        Route::get('/edit/{id}', [ShippingInstructionController::class, 'edit'])->name('shipping_instruction.edit');
        Route::post('/edit/{id}/update', [ShippingInstructionController::class, 'update'])->name('shipping_instruction.update');
        Route::post('/{id}/sailing_date/update', [ShippingInstructionController::class, 'updateSailingDate'])->name('shipping_instruction.sailing_date.update');
    });

    Route::group(['middleware' => ['check_permission:si.edit-cabang']], function () {
        Route::post('/{id}/berthing_date/update', [ShippingInstructionController::class, 'updateBerthingDate'])->name('shipping_instruction.berthing_date.update');
        Route::post('/operation/{id}/{status}/update', [ShippingInstructionController::class, 'updateOperation'])->name('shipping_instruction.operation.update');
    });

    Route::group(['middleware' => ['check_permission:si.edit-si', 'check_permission:si.edit-cabang']], function () {
        Route::post('operation/revert', [ShippingInstructionController::class, 'revertMonitorStatus'])->name('shipping_instruction.operation.revert');
    });

    Route::post('/{id}/status/update', [ShippingInstructionController::class, 'updateStatus'])->name('shipping_instruction.satus.update');

    Route::group(['middleware' => ['check_permission:si.print']], function () {
        Route::get('/print/{id}', [ShippingInstructionController::class, 'printPreview'])->name('shipping_instruction.print');
        Route::get('/si_final/export/{id}', [ShippingInstructionController::class, 'exportFinalDocument'])->name('shipping_instruction.final.export');
        Route::get('/export/{id}', [ShippingInstructionController::class, 'exportReport'])->name('shipping_instruction.export');
        Route::get('/export_list', [ShippingInstructionController::class, 'exportList'])->name('shipping_instruction.list.export');
        Route::get('/operation/export_list', [ShippingInstructionController::class, 'exportOperation'])->name('shipping_instruction.operation.export');
    });

    Route::group(['middleware' => ['check_permission:si.delete']], function () {
        Route::post('/{id}/void', [ShippingInstructionController::class, 'void'])->name('shipping_instruction.void');
        Route::post('/{siId}/do/void', [ShippingInstructionController::class, 'voidDo'])->name('shipping_instruction.do.void');
    });

    Route::group(['middleware' => ['check_permission:si.document-download']], function () {
        Route::get('{id}/uploads/view/', [ShippingInstructionController::class, 'viewUploads'])->name('shipping_instruction.upload.view');
        Route::get('document/{id}/download', [ShippingInstructionController::class, 'downloadDocument'])->name('shipping_instruction.document.download');
    });

    Route::group(['middleware' => ['check_permission:si.document-upload']], function () {
        Route::get('{id}/uploads/form/', [ShippingInstructionController::class, 'formUpload'])->name('shipping_instruction.upload.form');
        Route::post('uploads/submit', [ShippingInstructionController::class, 'submitUploads'])->name('shipping_instruction.upload.submit');
    });

    Route::group(['middleware' => ['check_permission:si.document-delete']], function () {
        Route::post('document/delete', [ShippingInstructionController::class, 'deleteDocument'])->name('shipping_instruction.document.delete');
    });

    Route::get('/select2', [ShippingInstructionController::class, 'select2'])->name('shipping_instruction.select2');
    Route::get('/trucking_company/select2', [ShippingInstructionController::class, 'select2Company'])->name('shipping_instruction.trucking_company.select2');

    // TEMPORARY - recount container totals for an SI header
    Route::get('/recount/{id}', [ShippingInstructionController::class, 'recountTotals'])->name('shipping_instruction.recount');
});
