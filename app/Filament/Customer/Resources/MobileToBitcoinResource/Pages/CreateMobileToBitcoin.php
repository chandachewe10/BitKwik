<?php

namespace App\Filament\Customer\Resources\MobileToBitcoinResource\Pages;

use App\Filament\Customer\Resources\MobileToBitcoinResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Filament\Support\Exceptions\Halt;

class CreateMobileToBitcoin extends CreateRecord
{
    protected static string $resource = MobileToBitcoinResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    //dd($data);
    $reference = 'TXN_' . uniqid() . '_' . time();
    $data['reference'] = $reference;
    
    // Store data in cache and session
    Cache::put("pending_transaction_{$reference}", $data, now()->addMinutes(30));
    session()->put('pending_transaction_data', $data);
    
    // Redirect to payment page
    $this->redirect(route('subscription.lenco'));
    
    // Stop the creation process
    throw new Halt();
}
    protected function handleRecordCreation(array $data): Model
    {
        // This won't be called due to the Halt exception above
        return new (static::getModel());
    }
}