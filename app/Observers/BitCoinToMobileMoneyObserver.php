<?php

namespace App\Observers;

use App\Models\BitCoinToMobileMoney;

class BitCoinToMobileMoneyObserver
{
    /**
     * Handle the BitCoinToMobileMoney "created" event.
     */
    public function created(BitCoinToMobileMoney $bitCoinToMobileMoney): void
    {
        $bitCoinToMobileMoney->user_id = auth()->user()->id;
        $bitCoinToMobileMoney->save();
    }

    /**
     * Handle the BitCoinToMobileMoney "updated" event.
     */
    public function updated(BitCoinToMobileMoney $bitCoinToMobileMoney): void
    {
        //
    }

    /**
     * Handle the BitCoinToMobileMoney "deleted" event.
     */
    public function deleted(BitCoinToMobileMoney $bitCoinToMobileMoney): void
    {
        //
    }

    /**
     * Handle the BitCoinToMobileMoney "restored" event.
     */
    public function restored(BitCoinToMobileMoney $bitCoinToMobileMoney): void
    {
        //
    }

    /**
     * Handle the BitCoinToMobileMoney "force deleted" event.
     */
    public function forceDeleted(BitCoinToMobileMoney $bitCoinToMobileMoney): void
    {
        //
    }
}
