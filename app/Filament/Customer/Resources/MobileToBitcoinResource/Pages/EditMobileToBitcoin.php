<?php

namespace App\Filament\Customer\Resources\MobileToBitcoinResource\Pages;

use App\Filament\Customer\Resources\MobileToBitcoinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMobileToBitcoin extends EditRecord
{
    protected static string $resource = MobileToBitcoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
