<?php

namespace App\Http\Controllers\API\LNbits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BitCoinToBankAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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


        return response()->json(['status' => 'success', 'payment' => $payment]);
    }
}
