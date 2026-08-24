<?php

use App\Http\Controllers\ScriptController;
use Illuminate\Support\Facades\Route;

Route::get('/script/test_oracle', [ScriptController::class, 'testOracle']);
Route::get('/script/fill_branch_destination_id', [ScriptController::class, 'fillBranchDestinationId']);
Route::get('/script/sync_si_to_container', [ScriptController::class, 'syncSiToContainer']);
Route::get('/script/simulate_timbangan_in', [ScriptController::class, 'simulateTimbanganIn']);
