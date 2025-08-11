<?php

namespace App\Http\Controllers\API\LNbits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BitCoinToBankAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class ConfirmBitCoinToBankController extends Controller
{


    public function confirmBitCoinToBankPayments(Request $request)
    {

        
        Log::info('LNbits Webhook Received For BitCoin To Bank:', $request->all());

        
        $data = $request->validate([
            'checking_id' => 'required|string|exists:bit_coin_to_bank_accounts,checking_id',
            'pending'       => 'required|boolean',
            
        ]);

       
        $payment = BitCoinToBankAccount::updateOrCreate(
            ['checking_id' => $data['checking_id']],
            [
                'payment_status' => $data['pending'] ? 'pending' : 'paid',
                'paid_at'        => $data['pending'] ? null : Carbon::now(),
            ]
        );

if($data['pending']){

    $response = Http::withHeaders([
    'Authorization' => 'Bearer ' . config('services.lenco.token'),
    'Accept'        => 'application/json',
    'Content-Type'  => 'application/json',
])->post(config('services.lenco.base_uri') . '/transfers/mobile-money', [
    'accountId'           => config('services.lenco.wallet_uuid'),
    'amount'               => 6,
    'narration'            => 'BitCoin Transfer to Mobile Money',
    'reference'            => $payment->id.'-'.mt_rand(),
    'transferRecipientId'  => '', 
    'phone'                => '0973750029',
    'operator'             => 'airtel',
    'country'              => 'zm',
]);

   if ($response->successful()) {
       Log::info('Lenco Mobile Money Transfer Successful:', $response->json());
   } else {
       Log::error('Lenco Mobile Money Transfer Failed:', $response->json());
   }
}
        return response()->json(['status' => 'success', 'payment' => $payment]);
    }
}
