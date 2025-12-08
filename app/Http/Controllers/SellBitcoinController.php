<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\BitCoinToMobileMoney;

class SellBitcoinController extends Controller
{
    public function generateInvoice(Request $request)
    {
        try {
            // Validate request data
            $data = $request->validate([
                'phone' => 'required|string',
                'amount_sats' => 'required|numeric|min:200',
                'amount_btc' => 'required|numeric',
                'amount_kwacha' => 'required|numeric',
                'conversion_fee' => 'required|numeric',
                'total_sats' => 'required|numeric|min:200',
                'network_fee' => 'required|numeric',
            ]);

            // Check OpenNode configuration
            $apiKey = config('services.opennode.api_key');
            $baseUri = config('services.opennode.base_uri');
            
            if (!$apiKey || !$baseUri) {
                Log::error('OpenNode configuration missing');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment gateway configuration error. Please contact support.',
                ], 500);
            }

            // Generate Lightning invoice using OpenNode
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($baseUri . '/charges', [
                'amount' => (int) $data['total_sats'],
                'description' => 'Bitcoin to Kwacha - ' . $data['phone'],
                'customer_name' => 'Customer',
                'customer_email' => 'customer@bitkwik.com',
                'order_id' => 'sell_' . time(),
                'callback_url' => config('services.opennode.mobile_money'),
                'success_url' => env('APP_URL'),
                'auto_settle' => true,
                'ttl' => 10,
            ]);

            if (!$response->successful()) {
                Log::error('OpenNode invoice generation failed: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate invoice. Please try again.',
                    'details' => $response->json(),
                ], 400);
            }

            $json = $response->json()['data'] ?? [];
            $bolt11 = $json['lightning_invoice']['payreq'] ?? null;
            $checkingId = $json['id'] ?? null;

            if (!$bolt11) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invoice generation failed. Please try again.',
                ], 400);
            }

            // Generate QR code
            $qrCodeFileName = 'sell_bitcoin_' . time() . '.svg';
            $qrCodeImage = QrCode::format('svg')->size(400)->generate($bolt11);
            $qrCodeDir = public_path('images/qrcodes');
            
            // Ensure directory exists
            if (!file_exists($qrCodeDir)) {
                mkdir($qrCodeDir, 0755, true);
            }
            
            $filePath = $qrCodeDir . '/' . $qrCodeFileName;
            file_put_contents($filePath, $qrCodeImage);

            // Save transaction to database
            BitCoinToMobileMoney::create([
                "user_id" => auth()->check() ? auth()->id() : null,
                "amount_kwacha" => $data['amount_kwacha'],
                "amount_sats" => $data['amount_sats'],
                "amount_btc" => $data['amount_btc'],
                "network_fee" => $data['network_fee'],
                "total_sats" => $data['total_sats'],
                "mobile_number" => $data['phone'],
                "convenience_fee" => $data['conversion_fee'],
                "customer_name" => 'Customer',
                "customer_phone" => $data['phone'],
                "delivery_email" => 'customer@bitkwik.com',
                'qr_code_path' => $qrCodeFileName,
                'lightning_invoice_address' => $bolt11,
                'checking_id' => $checkingId,
                'checkout_url' => $json['hosted_checkout_url'] ?? null,
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'bolt11' => $bolt11,
                'qr_code_path' => $qrCodeFileName,
                'checking_id' => $checkingId,
                'amount_kwacha' => $data['amount_kwacha'],
                'amount_sats' => $data['amount_sats'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check your input.',
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error generating invoice: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = config('app.debug') 
                ? $e->getMessage() . ' (File: ' . basename($e->getFile()) . ', Line: ' . $e->getLine() . ')'
                : 'An error occurred. Please try again.';
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred. Please try again.',
                'error' => $errorMessage,
                'details' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null,
            ], 500);
        }
    }
}

