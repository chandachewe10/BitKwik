<?php

namespace App\Filament\Customer\Resources\SendToBankResource\Pages;

use App\Filament\Customer\Resources\SendToBankResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSendToBanks extends ListRecords
{
    protected static string $resource = SendToBankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
