<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OPENNODE\ConfirmBitCoinToBankController;
use App\Http\Controllers\API\OPENNODE\ConfirmBitCoinToMobileController;



Route::post('/confirm-bitcoin-to-bank', [ConfirmBitCoinToBankController::class, 'confirmBitCoinToBankPayments']);
Route::post('/confirm-bitcoin-to-mobile', [ConfirmBitCoinToMobileController::class, 'confirmBitCoinToMobileMoneyPayments']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
