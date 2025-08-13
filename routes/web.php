<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailSubscriptionAndContactUsController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/emailSubscriptions',[EmailSubscriptionAndContactUsController::class,'emailSubscription'])
    ->name('emailSubscription');

Route::post('/contact', [EmailSubscriptionAndContactUsController::class,'contactUs'])
    ->name('contactUs');
