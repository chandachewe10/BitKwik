<?php 
namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class Paybill extends Widget
{
    protected static string $view = 'filament.customer.widgets.paybill';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true; 
    }
}
