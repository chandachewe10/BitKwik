<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lenco\PaymentController;
use App\Http\Controllers\SellBitcoinController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/subscription/payment', function () {
    $data = session()->get('pending_transaction_data');
    
    // If no session data, try to get from request (GET or POST)
    if (!$data) {
        $data = [
            'name' => request()->get('name', request()->input('name', 'Customer')),
            'email' => request()->get('email', request()->input('email', 'customer@bitkwik.com')),
            'phone' => request()->get('phone', request()->input('phone', '')),
            'amount_kwacha' => request()->get('amount_kwacha', request()->input('amount_kwacha', 0)),
            'total_amount' => request()->get('total_amount', request()->input('total_amount', 0)),
            'amount_sats' => request()->get('amount_sats', request()->input('amount_sats', 0)),
            'amount_btc' => request()->get('amount_btc', request()->input('amount_btc', 0)),
            'conversion_fee' => request()->get('conversion_fee', request()->input('conversion_fee', 0)),
            'network_fee' => request()->get('network_fee', request()->input('network_fee', 5)),
            'type' => request()->get('type', request()->input('type', 'buy')),
        ];
        session()->put('pending_transaction_data', $data);
    }
    
    return view('gateways.Lenco.LencoPayments', ['data' => $data]);
})->name('subscription.lenco');

Route::post('/subscription/payment', function () {
    $data = request()->all();
    session()->put('pending_transaction_data', $data);
    return redirect()->route('subscription.lenco');
});

Route::post('/complete-subscription', [PaymentController::class, 'completeSubscription'])
    ->name('complete.subscription');

Route::post('/generate-invoice', [SellBitcoinController::class, 'generateInvoice'])
    ->name('generate.invoice');