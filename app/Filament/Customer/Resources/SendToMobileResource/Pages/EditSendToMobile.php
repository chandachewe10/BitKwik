<?php

namespace App\Filament\Customer\Resources\SendToMobileResource\Pages;

use App\Filament\Customer\Resources\SendToMobileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSendToMobile extends EditRecord
{
    protected static string $resource = SendToMobileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
