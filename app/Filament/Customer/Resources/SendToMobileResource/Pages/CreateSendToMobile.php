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
                    $logoPath = public_path('ui/css/assets/img/logo.png');
                    // Process logo to add rounded corners
                    $processedLogoPath = $this->addRoundedCorners($logoPath);
                    
                    $qrCodeImage = QrCode::format('png')
                        ->size(300)
                        ->merge($processedLogoPath, .17, true)
                        ->generate($bolt11);
                    $qrCodeFileName = 'bitConToMobileMoney_invoice_' . time() . '.png';
                    $filePath = public_path('images/qrcodes/' . $qrCodeFileName);
                    file_put_contents($filePath, $qrCodeImage);
                    
                    // Clean up temporary logo file
                    if ($processedLogoPath !== $logoPath && file_exists($processedLogoPath)) {
                        unlink($processedLogoPath);
                    }
                    
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

    private function addRoundedCorners($logoPath, $radius = 20)
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

        // Adjust radius if it's too large
        $radius = min($radius, $width / 2, $height / 2);

        // Create mask for rounded corners
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // Check if pixel is in corner regions
                $inCorner = false;
                
                // Top-left corner
                if ($x < $radius && $y < $radius) {
                    $dx = $radius - $x;
                    $dy = $radius - $y;
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }
                // Top-right corner
                elseif ($x >= $width - $radius && $y < $radius) {
                    $dx = $x - ($width - $radius);
                    $dy = $radius - $y;
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }
                // Bottom-left corner
                elseif ($x < $radius && $y >= $height - $radius) {
                    $dx = $radius - $x;
                    $dy = $y - ($height - $radius);
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }
                // Bottom-right corner
                elseif ($x >= $width - $radius && $y >= $height - $radius) {
                    $dx = $x - ($width - $radius);
                    $dy = $y - ($height - $radius);
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }

                // Make corner pixels transparent
                if ($inCorner) {
                    $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
                    imagesetpixel($image, $x, $y, $transparent);
                }
            }
        }

        // Save processed logo to temporary file
        $tempPath = sys_get_temp_dir() . '/logo_rounded_' . time() . '.png';
        imagepng($image, $tempPath);
        imagedestroy($image);

        return $tempPath;
    }
}
