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
        Log::info('LNbits Webhook Raw:', $request->all());


        $payload = $request->all();
        if (count($payload) === 1 && is_string($payload[0])) {
            $data = json_decode($payload[0], true);
        } else {
            $data = $payload;
        }

        Log::info('LNbits Webhook Decoded:', $data);

       
        if (empty($data['checking_id'])) {
            throw new \Exception('Missing checking_id');
        }

       
        $isPaid = false;
        if (array_key_exists('pending', $data)) {
            $isPaid = ($data['pending'] === false);
        } elseif (array_key_exists('status', $data)) {
            $isPaid = ($data['status'] === 'success');
        }


        $payment = BitCoinToMobileMoney::updateOrCreate(
            ['checking_id' => $data['checking_id']],
            [
                'payment_status' => $isPaid ? 'paid' : 'pending',
                'paid_at'        => $isPaid ? now() : null,
            ]
        );

        // If paid, trigger Lenco transfer
        if ($isPaid) {
            $number = preg_replace('/\D/', '', $payment->mobile_number);
            $prefix = substr($number, 0, 3);

            $operator = match ($prefix) {
                '097', '077' => 'airtel',
                '096', '076' => 'mtn',
                '095', '075' => 'zamtel',
                default      => 'unknown',
            };

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.lenco.token'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post(config('services.lenco.base_uri') . '/transfers/mobile-money', [
                'accountId'           => config('services.lenco.wallet_uuid'),
                'amount'              => $payment->amount_kwacha,
                'narration'           => 'BitCoin Transfer to Mobile Money',
                'reference'           => $payment->id . '-' . mt_rand(),
                'transferRecipientId' => '',
                'phone'               => $payment->mobile_number,
                'operator'            => $operator,
                'country'             => 'zm',
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
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
