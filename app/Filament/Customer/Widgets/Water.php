<?php 
namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class Water extends Widget
{
    protected static string $view = 'filament.customer.widgets.water';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true; 
    }
}
