<?php

namespace App\Filament\Customer\Resources\MobileToBitcoinResource\Pages;

use App\Filament\Customer\Resources\MobileToBitcoinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMobileToBitcoins extends ListRecords
{
    protected static string $resource = MobileToBitcoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
