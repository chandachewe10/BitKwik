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

class CreateSendToMobile extends CreateRecord
{
    protected static string $resource = SendToMobileResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
           
            $totalSats = $data['total_sats'];
            $response = Http::withHeaders([
                'X-Api-Key' => config('services.lnbits.x-api-key'),
                'Content-Type' => 'application/json',
            ])->post(config('services.lnbits.base_uri') . '/payments', [
                'out' => false,
                'description' => 'BitCoin to Mobile Money',
                'amount' => $totalSats,
                'max' => 100000000,
                'min' => 0.00000001,
                'comment_chars' => 200,
                'username' => mt_rand(100000, 999999),
                'webhook' => config('services.lnbits.mobile_money'), 
            ]);

            $bolt11 = null;
            $checking_id = null;
            $qrCodeFileName = null;

            if ($response->successful()) {
                Log::info('LNbits Received For BitCoin To Mobile Money:', $response->json());

                $json = $response->json();
                $bolt11 = $json['bolt11'] ?? null;
                $checking_id = $json['checking_id'] ?? null;

                if ($bolt11) {
                    $qrCodeImage = QrCode::format('svg')->size(300)->generate($bolt11);
                    $qrCodeFileName = 'bitConToMobileMoney_invoice_' . time() . '.svg';
                    $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                    file_put_contents($filePath, $qrCodeImage);
                    $data['qr_code_path'] = $qrCodeFileName;
                    $data['bolt11'] = $bolt11;
                }
            }

            // Create the record in database
            return BitCoinToMobileMoney::create([
                "amount_kwacha" => $data['amount_kwacha'],
                "amount_sats" => $data['amount_sats'],
                "amount_btc" => $data['amount_btc'],
                "network_fee" => $data['network_fee'],
                "total_sats" => $data['total_sats'],
                "mobile_number" => $data['mobile_number'],
                "convenience_fee" => $data['conversion_fee'],
                "delivery_email" => $data['email'],
                'qr_code_path' => $qrCodeFileName,
                'lightning_invoice_address' => $bolt11,
                'checking_id' => $checking_id
            ]);

        } catch (Exception $e) {
            // Log the exception (optional)
            \Log::error('Error creating SendToMobile record: ' . $e->getMessage());

            // Throw a Filament notification error to the frontend
            Notification::make()
                ->danger()
                ->title('Failed to generate invoice')
                ->body('An error occurred: ' . $e->getMessage())
                ->send();

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
