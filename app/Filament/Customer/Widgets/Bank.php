<?php 
namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class Bank extends Widget
{
    protected static string $view = 'filament.customer.widgets.zanaco';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true; 
    }
}
