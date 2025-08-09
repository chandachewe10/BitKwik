<?php

namespace App\Filament\Customer\Resources\ZescoBillUnitsResource\Pages;

use App\Filament\Customer\Resources\ZescoBillUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZescoBillUnits extends EditRecord
{
    protected static string $resource = ZescoBillUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
