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

        // Reduced retry logic since record should exist now
        $payment = null;
        $maxAttempts = 3;
        $delay = 1;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $payment = BitCoinToMobileMoney::where('checking_id', $data['checking_id'])->first();
            
            if ($payment) {
                Log::info('Payment found on attempt ' . $attempt);
                break;
            }

            if ($attempt < $maxAttempts) {
                Log::info('Payment not found, attempt ' . $attempt . ', retrying in ' . $delay . ' seconds');
                sleep($delay);
                $delay *= 2;
            }
        }

        if (!$payment) {
            Log::warning('Webhook with invalid checking_id received after ' . $maxAttempts . ' attempts', [
                'checking_id' => $data['checking_id']
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid checking_id - payment not found',
            ], 400);
        }

        // Rest of your webhook logic...
        $isPaid = ($data['status'] === 'success');

        $payment->update([
            'payment_status' => $isPaid ? 'paid' : 'pending',
            'paid_at'        => $isPaid ? now() : null,
        ]);

        if ($isPaid) {
            $this->processMobileMoneyTransfer($payment);
        }

        return response()->json(['status' => 'success', 'payment' => $payment]);

    } catch (\Throwable $e) {
        Log::error('Webhook Exception:', ['message' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
}