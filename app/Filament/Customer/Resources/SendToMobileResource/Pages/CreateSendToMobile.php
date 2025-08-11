<?php

namespace App\Filament\Customer\Resources\SendToMobileResource\Pages;

use App\Filament\Customer\Resources\SendToMobileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\BitCoinToMobileMoney;
use Filament\Notifications\Notification;

class CreateSendToMobile extends CreateRecord
{
    protected static string $resource = SendToMobileResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        //dd($data);
        $satsAmount = $data['amount_sats'];
        $serviceFee = $data['conversion_fee'];
        $totalSats = $satsAmount + $serviceFee;
        $response = Http::withHeaders([
            'X-Api-Key' => '8d2e440297bc4763803ed6a6ba62d285',
            'Content-Type' => 'application/json',
        ])->post('https://demo.lnbits.com/api/v1/payments', [
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

            if ($bolt11) {
                $qrCodeImage = QrCode::format('svg')->size(300)->generate($bolt11);
                $fileName = 'bitConToMobileMoney_invoice_' . time() . '.svg';
                $filePath = public_path('images/qrcodes/' . $fileName);
                file_put_contents($filePath, $qrCodeImage);
                $data['qr_code_path'] = $fileName;
                $data['bolt11'] = $bolt11;
            }
        }




        return BitCoinToMobileMoney::create([
            "amount_kwacha" =>  $data['amount_kwacha'],
            "amount_sats" => $data['amount_sats'],
            "amount_btc" => $data['amount_btc'],
            "conversion_fee" => $data['conversion_fee'],
            "mobile_number" => $data['mobile_number'],
            "delivery_email" => $data['email'],
            'qr_code_path' => $data['qr_code_path'],
            'lightning_invoice_address' => $bolt11 ?? NULL,
        ]);
    }
}
