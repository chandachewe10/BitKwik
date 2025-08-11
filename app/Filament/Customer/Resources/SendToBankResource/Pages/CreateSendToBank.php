<?php

namespace App\Filament\Customer\Resources\SendToBankResource\Pages;

use App\Filament\Customer\Resources\SendToBankResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\BitCoinToBankAccount;
use Filament\Notifications\Notification;

class CreateSendToBank extends CreateRecord
{
    protected static string $resource = SendToBankResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        //dd($data);
        $satsAmount = $data['amount_sats'];
        $serviceFee = $data['conversion_fee'];
        $totalSats = $satsAmount + $serviceFee;
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
        ]);

        if ($response->successful()) {
            $json = $response->json();
            $bolt11 = $json['bolt11'] ?? null;
            $checking_id = $json['checking_id'] ?? null;      

            if ($bolt11) {
                $qrCodeImage = QrCode::format('svg')->size(300)->generate($bolt11);
                $fileName = 'bitConToMobileMoney_invoice_' . time() . '.svg';
                $filePath = public_path('images/qrcodes/' . $fileName);
                file_put_contents($filePath, $qrCodeImage);
                $data['qr_code_path'] = $fileName;
                $data['bolt11'] = $bolt11;
            }
        }




        return BitCoinToBankAccount::create([
            "amount_kwacha" =>  $data['amount_kwacha'],
            "amount_sats" => $data['amount_sats'],
            "amount_btc" => $data['amount_btc'],
            "conversion_fee" => $data['conversion_fee'],
            "account_number" => $data['account_number'],
            "bank_name" => $data['bank_name'],
            "bank_branch" => $data['bank_branch'],
            "bank_sort_code" => $data['bank_sort_code'],
            "bank_account_type" => $data['bank_account_type'],
            "convenience_fee" => $data['conversion_fee'],
            "delivery_email" => $data['email'] ?? NULL,
            'qr_code_path' => $data['qr_code_path'] ?? NULL,
            'lightning_invoice_address' => $bolt11 ?? NULL,
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
