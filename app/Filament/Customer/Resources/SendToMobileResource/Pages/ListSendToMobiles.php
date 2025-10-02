<?php

namespace App\Filament\Customer\Resources\SendToMobileResource\Pages;

use App\Filament\Customer\Resources\SendToMobileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSendToMobiles extends ListRecords
{
    protected static string $resource = SendToMobileResource::class;

    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
