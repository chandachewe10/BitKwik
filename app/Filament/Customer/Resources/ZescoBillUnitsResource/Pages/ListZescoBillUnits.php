<?php

namespace App\Filament\Customer\Resources\ZescoBillUnitsResource\Pages;

use App\Filament\Customer\Resources\ZescoBillUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListZescoBillUnits extends ListRecords
{
    protected static string $resource = ZescoBillUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
         //   Actions\CreateAction::make(),
        ];
    }
}
