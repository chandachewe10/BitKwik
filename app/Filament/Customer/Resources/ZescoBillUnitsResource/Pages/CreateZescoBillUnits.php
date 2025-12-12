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
                // Process logo to make it circular
                $processedLogoPath = $this->makeLogoCircular($logoPath);
                
                $qrCodeImage = QrCode::format('png')
                    ->size(300)
                    ->merge($processedLogoPath, .2, true)
                    ->generate($bolt11);

                $fileName = 'zesco_invoice_' . time() . '.png';
                $filePath = public_path('images/qrcodes/' . $fileName);
                file_put_contents($filePath, $qrCodeImage);
                
                // Clean up temporary logo file
                if ($processedLogoPath !== $logoPath && file_exists($processedLogoPath)) {
                    unlink($processedLogoPath);
                }

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

    private function makeLogoCircular($logoPath)
    {
        if (!file_exists($logoPath) || !function_exists('imagecreatefrompng')) {
            return $logoPath;
        }

        $image = imagecreatefrompng($logoPath);
        if (!$image) {
            return $logoPath;
        }

        // Enable alpha blending and save alpha
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Get image dimensions
        $width = imagesx($image);
        $height = imagesy($image);
        $size = min($width, $height);

        // Create a new square image with transparent background
        $circularImage = imagecreatetruecolor($size, $size);
        imagealphablending($circularImage, false);
        imagesavealpha($circularImage, true);
        $transparent = imagecolorallocatealpha($circularImage, 255, 255, 255, 127);
        imagefill($circularImage, 0, 0, $transparent);

        // Calculate center and radius
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;

        // Copy pixels within the circle
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $dx = $x - $centerX;
                $dy = $y - $centerY;
                $distance = sqrt($dx * $dx + $dy * $dy);

                if ($distance <= $radius) {
                    // Calculate source coordinates (center the original image)
                    $srcX = ($width - $size) / 2 + $x;
                    $srcY = ($height - $size) / 2 + $y;

                    if ($srcX >= 0 && $srcX < $width && $srcY >= 0 && $srcY < $height) {
                        $rgb = imagecolorat($image, (int)$srcX, (int)$srcY);
                        imagesetpixel($circularImage, $x, $y, $rgb);
                    }
                }
            }
        }

        imagedestroy($image);

        // Save processed logo to temporary file
        $tempPath = sys_get_temp_dir() . '/logo_circular_' . time() . '.png';
        imagepng($circularImage, $tempPath);
        imagedestroy($circularImage);

        return $tempPath;
    }
}
