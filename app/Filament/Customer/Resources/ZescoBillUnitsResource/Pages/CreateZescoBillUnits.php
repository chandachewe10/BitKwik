<?php

namespace App\Filament\Customer\Resources\ZescoBillUnitsResource\Pages;

use App\Filament\Customer\Resources\ZescoBillUnitsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\ZescoBills;
use Filament\Notifications\Notification;


class CreateZescoBillUnits extends CreateRecord
{
    protected static string $resource = ZescoBillUnitsResource::class;
    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        
        // dd($data);
        $response = Http::withHeaders([
            'X-Api-Key' => config('services.lnbits.x-api-key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.lnbits.base_uri').'/payments', [
            'out' => false,
            'description' => 'Zesco Units Bill Payments',
            'amount' => $data['amount_sats'],
            'max' => 100000000,
            'min' => 0.00000001,
            'comment_chars' => 200,
            'username' => mt_rand(100000, 999999),
        ]);

        if ($response->successful()) {
            $json = $response->json();
            $bolt11 = $json['bolt11'] ?? null;
            $checking_id = $json['checking_id'] ?? null; 

            if ($bolt11) {
                $logoPath = public_path('ui/css/assets/img/logo.png');
                $qrCodeImage = QrCode::format('png')
                    ->size(300)
                    ->merge($logoPath, .25, true)
                    ->generate($bolt11);

                $fileName = 'zesco_invoice_' . time() . '.png';
                $filePath = public_path('images/qrcodes/' . $fileName);
                file_put_contents($filePath, $qrCodeImage);

                // Store the file path and bolt11 in the DB record
                $data['qr_code_path'] = $fileName;
                $data['bolt11'] = $bolt11;
            }
        }




        return ZescoBills::create([
            'meter_number' => $data['meter_number'],
            'amount_kwacha' => $data['amount_kwacha'],
            'amount_sats' => $data['amount_sats'],
            'amount_btc' => $data['amount_btc'],
            'qr_code_path' => $data['qr_code_path'],
            "convenience_fee" => $data['conversion_fee'],
            'lightning_invoice_address' => $bolt11 ?? NULL,
            "delivery_email" => $data['email'],
            'checking_id' => $checking_id

        ]);
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
