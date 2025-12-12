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
                    // Process logo to make it circular
                    $processedLogoPath = $this->makeLogoCircular($logoPath);
                    
                    $qrCodeImage = QrCode::format('png')
                        ->size(300)
                        ->merge($processedLogoPath, .2, true)
                        ->generate($bolt11);
                    $qrCodeFileName = 'bitConToBank_invoice_' . time() . '.png';
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
