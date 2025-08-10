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
            'X-Api-Key' => '8d2e440297bc4763803ed6a6ba62d285',
            'Content-Type' => 'application/json',
        ])->post('https://demo.lnbits.com/api/v1/payments', [
            'out' => false,
            'description' => 'Zesco Units Bill Payments',
            'amount' => $data['amount_kwacha'] ?? 10,
            'max' => 500,
            'min' => 50,
            'comment_chars' => 200,
            'username' => mt_rand(100000, 999999),
        ]);

        if ($response->successful()) {
            $json = $response->json();
            $bolt11 = $json['bolt11'] ?? null;

            if ($bolt11) {
                $qrCodeImage = QrCode::format('svg')->size(300)->generate($bolt11);

                //$qrCodeImage = QrCode::format('png')->size(300)->generate($bolt11);
                $fileName = 'zesco_invoice_' . time() . '.svg';
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
            'qr_code_path' => $data['qr_code_path'],
            'bolt11' => $data['bolt11'],
            'lightning_invoice_address' => $bolt11 ?? NULL,

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
            ->title('Zesco Bill created')
            ->body('The Zesco Bill has been created successfully. Please check your email for the invoice.');
    }
}
