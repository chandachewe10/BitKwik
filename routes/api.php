<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OPENNODE\ConfirmBitCoinToBankController;
use App\Http\Controllers\API\OPENNODE\ConfirmBitCoinToMobileController;
use App\Http\Controllers\Lenco\ConfirmMobileToBitcoinController;



Route::post('/confirm-bitcoin-to-bank', [ConfirmBitCoinToBankController::class, 'confirmBitCoinToBankPayments']);
Route::post('/confirm-bitcoin-to-mobile', [ConfirmBitCoinToMobileController::class, 'confirmBitCoinToMobileMoneyPayments']);
Route::post('/confirm-mobile-to-bitcoin', [ConfirmMobileToBitcoinController::class, 'confirmMobileToBitcoinPayments']);
