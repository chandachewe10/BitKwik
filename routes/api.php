<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LNbits\ConfirmBitCoinToBankController;
use App\Http\Controllers\API\LNbits\ConfirmBitCoinToMobileController;



Route::get('/confirm-bitcoin-to-bank', [ConfirmBitCoinToBankController::class, 'confirmBitCoinToBankPayments']);
Route::get('/confirm-bitcoin-to-mobile', [ConfirmBitCoinToMobileController::class, 'confirmBitCoinToMobileMoneyPayments']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
