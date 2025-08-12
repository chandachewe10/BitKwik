<?php

namespace App\Observers;

use App\Models\BitCoinToBankAccount;

class BitCoinToBankAccountObserver
{
    /**
     * Handle the BitCoinToBankAccount "created" event.
     */
    public function created(BitCoinToBankAccount $bitCoinToBankAccount): void
    {
            $bitCoinToBankAccount->user_id = auth()->user()->id;
            $bitCoinToBankAccount->save();
    }

    /**
     * Handle the BitCoinToBankAccount "updated" event.
     */
    public function updated(BitCoinToBankAccount $bitCoinToBankAccount): void
    {
        //
    }

    /**
     * Handle the BitCoinToBankAccount "deleted" event.
     */
    public function deleted(BitCoinToBankAccount $bitCoinToBankAccount): void
    {
        //
    }

    /**
     * Handle the BitCoinToBankAccount "restored" event.
     */
    public function restored(BitCoinToBankAccount $bitCoinToBankAccount): void
    {
        //
    }

    /**
     * Handle the BitCoinToBankAccount "force deleted" event.
     */
    public function forceDeleted(BitCoinToBankAccount $bitCoinToBankAccount): void
    {
        //
    }
}
