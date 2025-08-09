<?php

namespace App\Filament\Customer\Resources\ZescoBillUnitsResource\Pages;

use App\Filament\Customer\Resources\ZescoBillUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateZescoBillUnits extends CreateRecord
{
    protected static string $resource = ZescoBillUnitsResource::class;
     protected static bool $canCreateAnother = false;
}
