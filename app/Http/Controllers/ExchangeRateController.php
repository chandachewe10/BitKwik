<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateController extends Controller
{
    /**
     * Fetch live exchange rates from OpenNode API
     */
    public function getRates()
    {
        try {
            $token = config('services.opennode.exchange_rates_token');
            
            if (!$token) {
                Log::error('Exchange rates token not configured');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exchange rates service not configured',
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get('https://api.opennode.com/v1/rates');

            if ($response->successful()) {
                $data = $response->json();
                
                // OpenNode returns rates in format: { "data": { "BTCZMW": { "ZMW": 2101052.9, "BTC": 4.8e-7, ... }, ... } }
                // Extract ZMW rate from BTCZMW entry
                $btcZmwRate = null;
                $btcUsdRate = null;
                
                if (isset($data['data']['BTCZMW']['ZMW'])) {
                    $btcZmwRate = (float) $data['data']['BTCZMW']['ZMW'];
                }
                
                // Also get USD rate for fallback
                if (isset($data['data']['BTCUSD']['USD'])) {
                    $btcUsdRate = (float) $data['data']['BTCUSD']['USD'];
                }
                
                return response()->json([
                    'status' => 'success',
                    'btc_zmw' => $btcZmwRate,
                    'btc_usd' => $btcUsdRate,
                ]);
            } else {
                Log::error('OpenNode rates API error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch exchange rates',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rates: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching exchange rates',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

