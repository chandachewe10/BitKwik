<?php

namespace App\Filament\Customer\Resources\SendToBankResource\Pages;

use App\Filament\Customer\Resources\SendToBankResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSendToBank extends EditRecord
{
    protected static string $resource = SendToBankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
