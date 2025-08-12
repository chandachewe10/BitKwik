<?php

namespace App\Providers;

use App\Models\BitCoinToBankAccount;
use App\Models\BitCoinToMobileMoney;
use App\Models\ZescoBills;
use App\Observers\BitCoinToBankAccountObserver;
use App\Observers\BitCoinToMobileMoneyObserver;
use App\Observers\ZescoBillsObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BitCoinToBankAccount::observe(BitCoinToBankAccountObserver::class);
        BitCoinToMobileMoney::observe(BitCoinToMobileMoneyObserver::class);
        ZescoBills::observe(ZescoBillsObserver::class);
    }
}
