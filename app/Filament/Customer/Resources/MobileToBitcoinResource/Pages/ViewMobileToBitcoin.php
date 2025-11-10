<?php

namespace App\Filament\Customer\Resources\MobileToBitcoinResource\Pages;

use App\Filament\Customer\Resources\MobileToBitcoinResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMobileToBitcoin extends ViewRecord
{
    protected static string $resource = MobileToBitcoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
