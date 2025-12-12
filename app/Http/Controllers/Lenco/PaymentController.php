<?php

namespace App\Http\Controllers\Lenco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\MobileToBitcoin;
use App\Services\WhatsAppService;

class PaymentController extends Controller
{
    public function completeSubscription(Request $request)
    {
        try {
            // For mobile app, accept data directly from request body
            // For web, use session data or request data
            $amount = (float) ($request->input('amount') ?? $request->input('amount_kwacha', 0));
            $paymentData = $request->has('payment_data') 
                ? (is_array($request->input('payment_data')) 
                    ? $request->input('payment_data') 
                    : json_decode($request->input('payment_data'), true))
                : [];
            $reference = 'ref-' . now()->timestamp;

            // Get session data for additional fields (web requests)
            $sessionData = session()->get('pending_transaction_data', []);
            
            // For mobile app, get data from request body; for web, use session or payment data
            $customerEmail = $request->input('email') 
                ?? $paymentData['customer']['email'] 
                ?? $sessionData['email'] 
                ?? 'customer@bitkwik.com';
            $customerName = $request->input('name') 
                ?? $paymentData['customer']['firstName'] 
                ?? $sessionData['name'] 
                ?? 'Customer';
            $phone = $request->input('phone') 
                ?? $paymentData['customer']['phone'] 
                ?? $sessionData['phone'] 
                ?? '';
            
            // Get amounts from request (mobile app) or session (web)
            $amountSats = $request->input('amount_sats') ?? $sessionData['amount_sats'] ?? null;
            $amountBtc = $request->input('amount_btc') ?? $sessionData['amount_btc'] ?? null;
            
            // Calculate convenience_fee: use from request, session, or calculate as 8% of amount
            $convenienceFee = $request->input('conversion_fee') 
                ?? $request->input('convenience_fee')
                ?? $sessionData['conversion_fee'] 
                ?? ($amount * 0.08);
            $networkFee = $request->input('network_fee') 
                ?? $sessionData['network_fee'] 
                ?? 5; // Default network fee is 5 ZMW

            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.opennode.api_key_withdrawal'),
                'Content-Type' => 'application/json',
            ])->post(config('services.opennode.base_uri_withdrawal') . '/lnurl-withdrawal', [
                "min_amt"      => 50,
                "max_amt"      => (int) ($amountSats ?? 0),
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
                $logoPath = public_path('ui/css/assets/img/logo.png');
                $qrCodeImage = QrCode::format('png')
                    ->size(300)
                    ->merge($logoPath, .25, true)
                    ->generate($lnurl);
                $qrCodeFileName = 'mobileMoneyToBitcoin_' . time() . '.png';
                $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                file_put_contents($filePath, $qrCodeImage);
            }

            // Save to database (user_id is nullable for public transactions)
            $transaction = MobileToBitcoin::create([
                "user_id" => auth()->check() ? auth()->id() : null, // Only set if user is authenticated
                "checking_id" => $checkingId,
                "amount_kwacha" => $amount,
                "amount_sats" => $amountSats,
                "amount_btc" => $amountBtc,
                "phone_number" => $phone,
                "convenience_fee" => round($convenienceFee, 2),
                "network_fee" => $networkFee,
                "lnurl" => $lnurl,
                "qr_code_path" => $qrCodeFileName,
                "description" => $data['description'] ?? "Mobile to Bitcoin Transaction for " . $customerEmail,
                "payment_status" => 'paid',
            ]);

            // Send WhatsApp message with QR code and Lightning link
            // Use phone number from payment (user can change it in Lenco payment UI)
            $whatsappPhone = $phone;
            if ($whatsappPhone && $lnurl) {
                // Use absolute URL for QR code image
                $qrCodeUrl = $qrCodeFileName ? url('images/qrcodes/' . $qrCodeFileName) : null;
                try {
                    WhatsAppService::sendPaymentQRCode(
                        $whatsappPhone,
                        $lnurl,
                        $qrCodeUrl,
                        $amountSats ?? 0,
                        $amount
                    );
                    Log::info('WhatsApp notification sent', [
                        'phone' => $whatsappPhone,
                        'transaction_id' => $transaction->id
                    ]);
                } catch (\Exception $e) {
                    // Don't fail the request if WhatsApp fails
                    Log::error('Failed to send WhatsApp notification: ' . $e->getMessage(), [
                        'phone' => $whatsappPhone,
                        'transaction_id' => $transaction->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'lnurl' => $lnurl,
                'qr_code_path' => $qrCodeFileName,
                'qr_code_url' => $qrCodeFileName ? asset('images/qrcodes/' . $qrCodeFileName) : null,
                'checking_id' => $checkingId,
                'message' => 'Payment initiated successfully. Please scan the QR code to complete the transaction.',
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
