<?php

namespace App\Filament\Customer\Resources\WaterBillUnitsResource\Pages;

use App\Filament\Customer\Resources\WaterBillUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaterBillUnits extends EditRecord
{
    protected static string $resource = WaterBillUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
