<?php

namespace App\Filament\Customer\Resources\SendToMobileResource\Pages;

use App\Filament\Customer\Resources\SendToMobileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\BitCoinToMobileMoney;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateSendToMobile extends CreateRecord
{
    protected static string $resource = SendToMobileResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::beginTransaction();
        
        try {
            // FIRST create the database record with checking_id as NULL initially
            $record = BitCoinToMobileMoney::create([
                "amount_kwacha" => $data['amount_kwacha'],
                "amount_sats" => $data['amount_sats'],
                "amount_btc" => $data['amount_btc'],
                "network_fee" => $data['network_fee'],
                "total_sats" => $data['total_sats'],
                "mobile_number" => $data['mobile_number'],
                "convenience_fee" => $data['conversion_fee'],
                "delivery_email" => $data['email'],
                'qr_code_path' => null,
                'lightning_invoice_address' => null,
                'checking_id' => null, // Will be updated after API call
                'payment_status' => 'pending'
            ]);

            // NOW make the API call to LNbits
            $totalSats = $data['total_sats'];
            $response = Http::withHeaders([
                'X-Api-Key' => config('services.lnbits.x-api-key'),
                'Content-Type' => 'application/json',
            ])->timeout(30) // Add timeout
            ->post(config('services.lnbits.base_uri') . '/payments', [
                'out' => false,
                'description' => 'BitCoin to Mobile Money - ' . $data['mobile_number'], // Include mobile in description
                'amount' => $totalSats,
                'max' => 100000000,
                'min' => 0.00000001,
                'comment_chars' => 200,
                'username' => mt_rand(100000, 999999),
                'webhook' => config('services.lnbits.mobile_money'), 
            ]);

            if (!$response->successful()) {
                throw new Exception('LNbits API failed: ' . $response->body());
            }

            Log::info('LNbits Received For BitCoin To Mobile Money:', $response->json());

            $json = $response->json();
            $bolt11 = $json['bolt11'] ?? null;
            $checking_id = $json['checking_id'] ?? null;
            $payment_hash = $json['payment_hash'] ?? null;

            if (!$bolt11 || !$checking_id) {
                throw new Exception('Missing bolt11 or checking_id from LNbits response');
            }

            $qrCodeFileName = null;
            if ($bolt11) {
                $qrCodeImage = QrCode::format('svg')->size(300)->generate($bolt11);
                $qrCodeFileName = 'bitConToMobileMoney_invoice_' . time() . '.svg';
                $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                
                // Ensure directory exists
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }
                
                file_put_contents($filePath, $qrCodeImage);
            }

            // UPDATE the record with the invoice data
            $record->update([
                'qr_code_path' => $qrCodeFileName,
                'lightning_invoice_address' => $bolt11,
                'checking_id' => $checking_id,
                'payment_hash' => $payment_hash
            ]);

            DB::commit();

            return $record;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating SendToMobile record: ' . $e->getMessage());

            Notification::make()
                ->danger()
                ->title('Failed to generate invoice')
                ->body('An error occurred: ' . $e->getMessage())
                ->send();

            throw $e; // Re-throw to prevent the record from being created
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Invoice Generated')
            ->body('Please check your lightning invoice to make payments.');
    }
}