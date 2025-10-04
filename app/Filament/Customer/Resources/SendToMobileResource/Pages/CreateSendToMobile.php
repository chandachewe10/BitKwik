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


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.opennode.api_key'),
                'Content-Type' => 'application/json',
            ])->post(config('services.opennode.base_uri') . '/charges', [
                'amount' => $data['total_sats'],
                'description' => 'BitCoin to Mobile Money',
                'customer_name' => auth()->user()->name,
                'customer_name' => auth()->user()->email,
                'order_id' => '',
                'callback_url' => config('services.opennode.mobile_money'),
                'success_url' => env('APP_URL') . '/customer',
                'auto_settle' => true,
                'split_to_btc_bps' => 0,
                'ttl' => 10,
                'notify_receiver' => true

            ]);

            $bolt11 = null;
            $checking_id = null;
            $qrCodeFileName = null;

            if ($response->successful()) {

                Log::info('Opennode Received For BitCoin To Mobile Money:', $response->json());

                $json = $response->json()['data'];


                $bolt11 = $json['lightning_invoice']['payreq'] ?? null;

                $checking_id = $json['id'] ?? null;

                if ($bolt11) {
                    $qrCodeImage = QrCode::format('svg')->size(300)->generate($bolt11);
                    $qrCodeFileName = 'bitConToMobileMoney_invoice_' . time() . '.svg';
                    $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                    file_put_contents($filePath, $qrCodeImage);
                    $data['qr_code_path'] = $qrCodeFileName;
                    $data['bolt11'] = $bolt11;
                }
            }

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
                'checking_id' => $checking_id,
                'checkout_url' => $json['hosted_checkout_url'] ?? null,
            ]);
        } catch (Exception $e) {
            // Log the exception (optional)
            Log::error('Error creating SendToMobile record: ' . $e->getMessage());

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
