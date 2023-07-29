<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterCoaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[HomeController::class,'index'])->name('home');

Route::prefix('master-coa')->group(function(){
    Route::get('',[MasterCoaController::class,'index'])->name('master-coa.index');
    Route::get('accounts',[MasterCoaController::class,'getAccount'])->name('master-coa.accounts');
    Route::post('store',[MasterCoaController::class,'store'])->name('master-coa.store');
});
