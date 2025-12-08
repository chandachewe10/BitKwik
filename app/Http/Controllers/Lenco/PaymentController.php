<?php

namespace App\Http\Controllers\Lenco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\MobileToBitcoin;

class PaymentController extends Controller
{
    public function completeSubscription(Request $request)
    {
        try {
            $amount = (float) $request->input('amount');
            $paymentData = json_decode($request->input('payment_data'), true);
            $reference = 'ref-' . now()->timestamp;

            // Get session data for additional fields
            $sessionData = session()->get('pending_transaction_data', []);
            
            // Get customer info from payment data or session (no authentication required)
            $customerEmail = $paymentData['customer']['email'] ?? $sessionData['email'] ?? 'customer@bitkwik.com';
            $customerName = $paymentData['customer']['firstName'] ?? $sessionData['name'] ?? 'Customer';
            
            // Calculate convenience_fee: use from session (conversion_fee) or calculate as 8% of amount
            $convenienceFee = $sessionData['conversion_fee'] ?? ($amount * 0.08);
            $networkFee = $sessionData['network_fee'] ?? 5; // Default network fee is 5 ZMW

            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.opennode.api_key_withdrawal'),
                'Content-Type' => 'application/json',
            ])->post(config('services.opennode.base_uri_withdrawal') . '/lnurl-withdrawal', [
                "min_amt"      => 50,
                "max_amt"      => (int) ($sessionData['amount_sats'] ?? null),
                "callback_url" => config('services.opennode.withdrawal'),
                "external_id"  => $reference,
                "expiry_date"  => time() + (10 * 60), // 10 minutes
                "description"  => "Mobile to Bitcoin Transaction for " . $customerEmail,
            ]);

            if (!$response->successful()) {
                Log::error('OpenNode withdrawal failed: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'OpenNode API request failed.',
                    'details' => $response->json(),
                ], 400);
            }

            $data = $response->json()['data'] ?? [];
            $checkingId = $data['id'] ?? null;
            $lnurl = $data['lnurl'] ?? null;

            $qrCodeFileName = null;
            if ($lnurl) {
                $qrCodeImage = QrCode::format('svg')->size(300)->generate($lnurl);
                $qrCodeFileName = 'mobileMoneyToBitcoin_' . time() . '.svg';
                $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                file_put_contents($filePath, $qrCodeImage);
            }

            // Save to database (user_id is nullable for public transactions)
            MobileToBitcoin::create([
                "user_id" => auth()->check() ? auth()->id() : null, // Only set if user is authenticated
                "checking_id" => $checkingId,
                "amount_kwacha" => $amount,
                "amount_sats" => $sessionData['amount_sats'] ?? null,
                "amount_btc" => $sessionData['amount_btc'] ?? null,
                "phone_number" => $paymentData['customer']['phone'] ?? $sessionData['phone'] ?? '',
                "convenience_fee" => round($convenienceFee, 2),
                "network_fee" => $networkFee,
                "lnurl" => $lnurl,
                "qr_code_path" => $qrCodeFileName,
                "description" => $data['description'] ?? "Mobile to Bitcoin Transaction for " . $customerEmail,
                "payment_status" => 'paid',
            ]);

            return response()->json([
                'status' => 'success',
                'lnurl' => $lnurl,
                'qr_code_path' => $qrCodeFileName,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in completeSubscription: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
