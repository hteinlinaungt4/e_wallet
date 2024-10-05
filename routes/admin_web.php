<?php

use App\Http\Controllers\Backend\AdminUserController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WalletController;
use Illuminate\Support\Facades\Route;

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
Route::prefix('admin')->middleware('auth:admin_user')->group(function(){
    Route::get('/',[PageController::class,'home'])->name('admin.home');

    // adminuser
    Route::resource('admin_user',AdminUserController::class);
    Route::get('ssd/admin_user',[AdminUserController::class,'ssd'])->name('admin_user.ssd');

    // user
    Route::resource('user',UserController::class);
    Route::get('ssd/user',[UserController::class,'ssd'])->name('user.ssd');

    // wallet
    Route::get('wallet',[WalletController::class,'index'])->name('wallet');
    Route::get('ssd/wallet',[WalletController::class,'ssd'])->name('wallet.ssd');

    Route::get('add-amount',[WalletController::class,"addamount"])->name('addamount');
    Route::post('add-amount/store',[WalletController::class,"addamountstore"])->name('addamountstore');


});


