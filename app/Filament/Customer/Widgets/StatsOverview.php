<?php

namespace App\Filament\Customer\Widgets;

use App\Filament\Resources\StatsOverviewResource\Widgets\AdminChart;
use App\Models\BitCoinToBankAccount;
use App\Models\BitCoinToMobileMoney;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Carbon\CarbonImmutable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;


class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $maxHeight = '100px';
    protected static ?int $sort = 1;
    public function getColumns(): int
    {
        return 2;
    }


    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        return [


            Stat::make('Mobile Money', BitCoinToMobileMoney::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('user_id', auth()->id())
                ->where('payment_status', 'paid')
                ->count())
                ->description('BitCoin to Mobile Money')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info')
                ->url('customer/send-to-mobiles/create'),

            Stat::make('Bank Account', BitCoinToBankAccount::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('user_id', auth()->id())
                ->where('payment_status', 'paid')
                ->count(),)
                ->description('Bitcoin to Bank Account')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('primary')
                ->url('customer/send-to-banks/create'),











        ];
    }
}
