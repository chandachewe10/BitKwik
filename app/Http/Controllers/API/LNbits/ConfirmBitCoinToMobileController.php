<?php

namespace App\Http\Controllers\API\LNbits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BitCoinToMobileMoney;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ConfirmBitCoinToMobileController extends Controller
{


    public function confirmBitCoinToMobileMoneyPayments(Request $request)
    {

        
        Log::info('LNbits Webhook Received For BitCoin To Mobile Money:', $request->all());

        
        $data = $request->validate([
            'checking_id' => 'required|string|exists:bit_coin_to_mobile_money,checking_id',
            'pending'       => 'required|boolean',
            
        ]);

       
        $payment = BitCoinToMobileMoney::updateOrCreate(
            ['checking_id' => $data['checking_id']],
            [
                'payment_status' => $data['pending'] ? 'pending' : 'paid',
                'paid_at'        => $data['pending'] ? null : Carbon::now(),
            ]
        );


        return response()->json(['status' => 'success', 'payment' => $payment]);
    }
}
