<?php

namespace App\Http\Controllers\API\LNbits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ZescoBills;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ConfirmZescoBillsPaymentsController extends Controller
{


    public function confirmZescoBillsPayments(Request $request)
    {

        Log::info('LNbits Webhook Received For Zesco Bills To Mobile Money:', $request->all());

        
        $data = $request->validate([
            'checking_id' => 'required|string|exists:zesco_bills,checking_id',
            'pending'       => 'required|boolean',
            
        ]);

       
        $payment = ZescoBills::updateOrCreate(
            ['checking_id' => $data['checking_id']],
            [
                'payment_status' => $data['pending'] ? 'pending' : 'paid',
                'paid_at'        => $data['pending'] ? null : Carbon::now(),
            ]
        );


        return response()->json(['status' => 'success', 'payment' => $payment]);
    }
}
