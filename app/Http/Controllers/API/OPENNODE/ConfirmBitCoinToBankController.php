<?php

namespace App\Http\Controllers\API\OPENNODE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BitCoinToBankAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ConfirmBitCoinToBankController extends Controller
{
    public function confirmBitCoinToBankPayments(Request $request)
    {
        try {
            Log::info('OPENNODE Webhook Raw For BitCoin To Bank', ['payload' => $request->all()]);

           
            $data = $request->all();

            Log::info('OPENNODE Webhook Parsed For BitCoin To Bank', ['data' => $data]);

           
            if (!isset($data['id'])) {
                throw new \Exception('Missing id (expected as checking_id)');
            }

            $checkingId = $data['id'];

            $payment = BitCoinToBankAccount::where('checking_id', $checkingId)
                ->first();

            if (!$payment) {
                Log::warning('Webhook with invalid checking_id', [
                    'checking_id' => $checkingId
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid checking_id',
                ], 400);
            }

            
            $isPaid = false;
            if (isset($data['status'])) {
                $isPaid = ($data['status'] === 'paid');
            }


            
            if ($payment->payment_status === 'paid' && $payment->paid_at !== null) {
                Log::info('Webhook already processed for checking_id', ['checking_id' => $checkingId]);
                return response()->json(['status' => 'success', 'message' => 'already_processed'], 200);
            }

            
            $payment->update([
                'payment_status' => $isPaid ? 'paid' : 'pending',
                'paid_at'        => $isPaid ? now() : null,
            ]);

           
            if ($isPaid) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . config('services.lenco.token'),
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ])->post(config('services.lenco.base_uri') . '/transfers/mobile-money', [
                        'accountId'          => config('services.lenco.wallet_uuid'),
                        'amount'             => $payment->amount_kwacha,
                        'narration'          => 'BitCoin Transfer to Bank',
                        'reference'          => $payment->id . '-' . mt_rand(),
                        'transferRecipientId'=> null,
                        'accountNumber'      => $payment->account_number,
                        'bankId'             => $payment->bank_name,
                        'country'            => 'zm',
                    ]);

                    if ($response->successful()) {
                        Log::info('Lenco Bank Transfer Successful', [
                            'payment_id' => $payment->id,
                            'response'   => $response->json(),
                        ]);
                    } else {
                        Log::error('Lenco Bank Transfer Failed', [
                            'payment_id' => $payment->id,
                            'status'     => $response->status(),
                            'response'   => $response->json(),
                        ]);
                    }
                } catch (\Throwable $txError) {
                    Log::error('Exception during Lenco Bank Transfer', [
                        'payment_id' => $payment->id,
                        'error'      => $txError->getMessage(),
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'payment' => $payment], 200);

        } catch (\Throwable $e) {
            Log::error('Error confirming BitCoin to Bank payment', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
