<?php

namespace App\Http\Controllers\API\LNbits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BitCoinToMobileMoney;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class ConfirmBitCoinToMobileController extends Controller
{


    public function confirmBitCoinToMobileMoneyPayments(Request $request)
    {
        try {



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

            if ($data['pending'] === false) {

                $number = preg_replace('/\D/', '', $payment->mobile_number);

                $prefix = substr($number, 0, 3);

                $operator = match ($prefix) {
                    '097', '077' => 'airtel',
                    '096', '076' => 'mtn',
                    '095', '075' => 'zamtel',
                    default      => 'unknown'
                };


                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.lenco.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])->post(config('services.lenco.base_uri') . '/transfers/mobile-money', [
                    'accountId'           => config('services.lenco.wallet_uuid'),
                    'amount'               => $payment->amount_kwacha,
                    'narration'            => 'BitCoin Transfer to Mobile Money',
                    'reference'            => $payment->id . '-' . mt_rand(),
                    'transferRecipientId'  => '',
                    'phone'                => $payment->mobile_number,
                    'operator'             => $operator,
                    'country'              => 'zm',
                ]);

                if ($response->successful()) {
                    Log::info('Lenco Mobile Money Transfer Successful:', $response->json());
                } else {
                    Log::error('Lenco Mobile Money Transfer Failed:', $response->json());
                }
            }



            return response()->json(['status' => 'success', 'payment' => $payment]);
        } catch (\Throwable $e) {
            Log::error('API Call Exception:', ['message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'error', 'message' => 'An error occurred while processing the payment.'], 500);
    }
}
