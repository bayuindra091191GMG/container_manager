<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login/submit', [AuthController::class, 'login'])->name('login.submit');

Route::middleware(['auth'])->group(function (){
    Route::get('/', [DashboardController::class, 'index'] )->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/setrole', function (){
    $permissions = [];
    $menus = \App\Models\Menu::all();
    foreach ($menus as $menu){
        $actions = explode(',', $menu->actions);
        foreach ($actions as $action){
            $permissions[] = $menu->code . '.' . $action;

            $check = \Spatie\Permission\Models\Permission::where('name', $menu->code . '.' . $action)->first();
            if(!$check){
                \Spatie\Permission\Models\Permission::create([
                    'name' => $menu->code . '.' . $action,
                ]);
            }
        }
    }
});
