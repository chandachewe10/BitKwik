<?php

namespace App\Filament\Customer\Resources\ZescoBillUnitsResource\Pages;

use App\Filament\Customer\Resources\ZescoBillUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewZescoBillUnits extends ViewRecord
{
    protected static string $resource = ZescoBillUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
