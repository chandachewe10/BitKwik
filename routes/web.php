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

Route::get('/subscription/payment', function () {
    $data = session()->get('pending_transaction_data');
    return view('gateways.Lenco.LencoPayments', ['data' => $data]);
})->name('subscription.lenco');

Route::post('completeSubscription/{amount}',[SubscriptionsController::class,'completeSubscription'])
->name('completeSubscription');