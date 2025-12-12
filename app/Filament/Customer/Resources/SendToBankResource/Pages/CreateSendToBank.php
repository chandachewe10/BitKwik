<?php

namespace App\Filament\Customer\Resources\SendToBankResource\Pages;

use App\Filament\Customer\Resources\SendToBankResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\BitCoinToBankAccount;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class CreateSendToBank extends CreateRecord
{
    protected static string $resource = SendToBankResource::class;

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

                Log::info('Opennode Received For BitCoin To Mobile Bank:', $response->json());

                $json = $response->json()['data'];


                $bolt11 = $json['lightning_invoice']['payreq'] ?? null;

                $checking_id = $json['id'] ?? null;

                if ($bolt11) {
                    $logoPath = public_path('ui/css/assets/img/logo.png');
                    $qrCodeImage = QrCode::format('png')
                        ->size(300)
                        ->merge($logoPath, .2, true)
                        ->generate($bolt11);
                    $qrCodeFileName = 'bitConToBank_invoice_' . time() . '.png';
                    $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                    file_put_contents($filePath, $qrCodeImage);
                    $data['qr_code_path'] = $qrCodeFileName;
                    $data['bolt11'] = $bolt11;
                }
            }

            
            return BitCoinToBankAccount::create([
                "amount_kwacha" => $data['amount_kwacha'],
                "amount_sats" => $data['amount_sats'],
                "amount_btc" => $data['amount_btc'],
                "network_fee" => $data['network_fee'],
                "total_sats" => $data['total_sats'],
                "account_number" => $data['account_number'],
                "bank_name" => $data['bank_name'],
                "bank_branch" => $data['bank_branch'],
                "bank_sort_code" => $data['bank_sort_code'],
                "bank_account_type" => $data['bank_account_type'],
                "convenience_fee" => $data['conversion_fee'],
                "delivery_email" => $data['email'] ?? null,
                'qr_code_path' => $qrCodeFileName,
                'lightning_invoice_address' => $bolt11,
                'checking_id' => $checking_id,
                'checkout_url' => $json['hosted_checkout_url'] ?? null,
            ]);

        } catch (Exception $e) {
            Log::error('Error creating SendToBank record: ' . $e->getMessage());

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
