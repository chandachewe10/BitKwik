<?php

namespace App\Filament\Customer\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;

class CustomerDashboard extends \Filament\Pages\Dashboard
{


    public function getHeader(): ?View
    {
        return view('filament.customer.pages.partials.welcome');
    }
    public static function getNavigationLabel(): string
    {
        return 'Home';
    }

    public function getTitle(): string
    {
        return 'Bitcoin2Kwacha';
    }




}
