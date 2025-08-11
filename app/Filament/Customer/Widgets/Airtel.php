<?php 
namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class Airtel extends Widget
{
    protected static string $view = 'filament.customer.widgets.airtel';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true; 
    }
}
