<?php 
namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class Zesco extends Widget
{
    protected static string $view = 'filament.customer.widgets.zesco';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true; 
    }
}
