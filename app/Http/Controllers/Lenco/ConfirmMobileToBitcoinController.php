<?php

namespace App\Http\Controllers\Lenco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileToBitcoin;
use Illuminate\Support\Facades\Log;

class ConfirmMobileToBitcoinController extends Controller
{
    public function confirmMobileToBitcoinPayments(Request $request)
    {
        try {
            Log::info('OPENNODE Webhook Raw:', $request->all());

            $data = $request->all(); 

            Log::info('OPENNODE Webhook Parsed:', $data);

            if (empty($data['id'])) {
                throw new \Exception('Missing id (used as checking_id?)');
            }

            // Try to find payment by checking_id (webhook id)
            $checkingId = $data['id'];
            
            // Also check lnurl_withdrawal.id if it exists (it's a JSON string)
            $lnurlWithdrawalId = null;
            if (isset($data['lnurl_withdrawal']) && is_string($data['lnurl_withdrawal'])) {
                $lnurlWithdrawal = json_decode($data['lnurl_withdrawal'], true);
                if (isset($lnurlWithdrawal['id'])) {
                    $lnurlWithdrawalId = $lnurlWithdrawal['id'];
                    Log::info('Found lnurl_withdrawal.id:', ['id' => $lnurlWithdrawalId]);
                }
            }

            Log::info('Searching for payment with checking_id:', [
                'main_id' => $checkingId,
                'lnurl_withdrawal_id' => $lnurlWithdrawalId
            ]);

            // Try to find payment by main id first
            $payment = MobileToBitcoin::where('checking_id', $checkingId)
                ->first();
            
            // If not found and we have lnurl_withdrawal.id, try that
            if (!$payment && $lnurlWithdrawalId) {
                Log::info('Trying lnurl_withdrawal.id as checking_id');
                $payment = MobileToBitcoin::where('checking_id', $lnurlWithdrawalId)
                    ->first();
            }

            if (!$payment) {
                Log::warning('Invalid checking_id in webhook', [
                    'checking_id' => $data['id'],
                    'webhook_data' => $data
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid checking_id',
                ], 400);
            }

            $isConfirmed = false;
            if (isset($data['status'])) {
                $isConfirmed = ($data['status'] === 'confirmed');
            }

            $payment->update([
                'payment_status' => 'paid',
                'is_used'        => $isConfirmed,
            ]);

            return response()->json([
                'status'  => 'success',
                'payment' => $payment,
            ]);

        } catch (\Throwable $e) {
            Log::error('API Error:', ['message' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
