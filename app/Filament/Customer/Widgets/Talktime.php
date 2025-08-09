<?php 
namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class Talktime extends Widget
{
    protected static string $view = 'filament.customer.widgets.talktime';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true; 
    }
}
