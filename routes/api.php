<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::namespace('Api')->group(function(){
    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);

    Route::middleware('auth:api')->group(function(){
        Route::get('/profile',[ApiController::class,'profile']);
        Route::post('/logout',[AuthController::class,'logout']);
        Route::get('/transactions',[ApiController::class,'transaction']);
        Route::get('/transaction/{trxid}',[ApiController::class,'transactionDetail']);
        Route::get('/notifications',[ApiController::class,'notifications']);
        Route::get('/notification/{id}',[ApiController::class,'notificationdetail']);

        Route::get('account_verfity',[ApiController::class,'account_vertify']);
        Route::post('account_confirm',[ApiController::class,'account_confirm']);
        Route::post('account_complete',[ApiController::class,'account_complete']);


        Route::post('transferScan',[ApiController::class,'transferScan']);
        Route::post('transfer_comfirm_Scan',[ApiController::class,'transferComfirmScan']);
        Route::post('transfer_complete_Scan',[ApiController::class,'transferCompleteScan']);



    });

});
