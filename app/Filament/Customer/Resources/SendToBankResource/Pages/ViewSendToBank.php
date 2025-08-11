<?php

namespace App\Filament\Customer\Resources\SendToBankResource\Pages;

use App\Filament\Customer\Resources\SendToBankResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSendToBank extends ViewRecord
{
    protected static string $resource = SendToBankResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\EditAction::make(),
        ];
    }
}
