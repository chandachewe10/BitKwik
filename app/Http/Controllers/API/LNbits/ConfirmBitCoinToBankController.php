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
        try {
            Log::info('LNbits Webhook Raw For BitCoin To Bank', ['payload' => $request->all()]);

            $payload = $request->all();
            $data = $payload;

            if (count($payload) === 1) {
                $first = array_values($payload)[0];
                if (is_string($first)) {
                    $decoded = json_decode($first, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $data = $decoded;
                    } else {
                        $raw = $request->getContent();
                        $decodedRaw = json_decode($raw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedRaw)) {
                            $data = $decodedRaw;
                        }
                    }
                }
            }

            Log::info('LNbits Webhook Decoded For BitCoin To Bank', ['data' => $data]);

            if (empty($data['checking_id'])) {
                throw new \Exception('Missing checking_id');
            }

            // RETRY LOGIC: Attempt to find payment with exponential backoff
            $payment = null;
            $maxAttempts = 5;
            $delay = 1; // Start with 1 second

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $payment = BitCoinToBankAccount::where('checking_id', $data['checking_id'])->first();
                
                if ($payment) {
                    Log::info('Bank payment found on attempt ' . $attempt, [
                        'checking_id' => $data['checking_id']
                    ]);
                    break;
                }

                if ($attempt < $maxAttempts) {
                    Log::info('Bank payment not found, attempt ' . $attempt . ', retrying in ' . $delay . ' seconds', [
                        'checking_id' => $data['checking_id']
                    ]);
                    sleep($delay);
                    $delay *= 2; // Exponential backoff: 1s, 2s, 4s, 8s
                }
            }

            if (!$payment) {
                Log::warning('Webhook with invalid checking_id received after ' . $maxAttempts . ' attempts', [
                    'checking_id' => $data['checking_id']
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid checking_id - payment not found after retries',
                ], 400);
            }

            $isPaid = false;
            if (array_key_exists('pending', $data)) {
                $isPaid = ($data['pending'] === false || $data['pending'] === 0 || $data['pending'] === 'false');
            } elseif (array_key_exists('status', $data)) {
                $status = is_string($data['status']) ? strtolower($data['status']) : null;
                $isPaid = in_array($status, ['success', 'paid'], true);
            }

            // Check if already processed
            if ($payment->payment_status === 'paid' && !is_null($payment->paid_at)) {
                Log::info('LNbits webhook already processed for checking_id', [
                    'checking_id' => $data['checking_id']
                ]);
                return response()->json([
                    'status' => 'success', 
                    'message' => 'already_processed'
                ], 200);
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