<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Backend\PageController as BackendPageController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WalletController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\NotificationController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::get('admin/login',[AdminLoginController::class,'showLoginForm'])->name('admin.login');
Route::post('admin/login',[AdminLoginController::class,'login'])->name('admin.login');
Route::post('admin/logout',[AdminLoginController::class,'logout'])->name('admin.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/home',[PageController::class,'home'])->name('user.home');
    Route::get('user/profile',[UserController::class,'profile'])->name('user.profile');
    Route::get('user/changepassword',[UserController::class,'changepasswordpage'])->name('user.changepasswordpage');
    Route::post('user/changepassword',[UserController::class,'changepassword'])->name('user.changepassword');
    Route::get('user/wallet',[WalletController::class,'userwallet'])->name('user.wallet');

    // transfer
    Route::get('user/transfer',[PageController::class,'transfer'])->name('user.transfer');
    Route::post('user/transfer_comfirm',[PageController::class,'transferComfirm'])->name('user.transferComfirm');
    Route::get('user/verify_account',[PageController::class,'verify_account'])->name('user.verify_account');
    Route::get('/user/password_check',[PageController::class,'passwordCheck'])->name('user.passwordcheck');
    Route::post('user/transfer_complete',[PageController::class,'transfer_complete'])->name('user.transfer_complete');

    // transaction
    Route::get('/transaction',[PageController::class,'transaction'])->name('user.transaction');
    Route::get('/transaction/{uuid}',[PageController::class,'transactionDetail'])->name('user.transactionDetail');
    Route::get('/transactionHash',[PageController::class,'hashtransaction']);
    Route::get('pdf',[PageController::class,'pdf'])->name('pdf.generate');


    // myqr
    Route::get('receiveqr',[PageController::class,'myqr'])->name('receiveqr');
    Route::get('payqr',[PageController::class,'payqr'])->name('payqr');

    // scan
    Route::get('user/transferScan',[PageController::class,'transferScan'])->name('user.transferScan');
    Route::post('user/transfer_comfirm_Scan',[PageController::class,'transferComfirmScan'])->name('user.transferComfirmScan');
    Route::post('user/transfer_complete_Scan',[PageController::class,'transfer_complete_Scan'])->name('user.transfer_completeScan');


    //noti

    Route::get('user/notification',[NotificationController::class,'index'])->name('user.notification');
    Route::get('user/notification/{id}',[NotificationController::class,'show'])->name('user.notificationshow');


});
Route::redirect('/', '/login');




