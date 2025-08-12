<?php

namespace App\Observers;

use App\Models\ZescoBills;

class ZescoBillsObserver
{
    /**
     * Handle the ZescoBills "created" event.
     */
    public function created(ZescoBills $zescoBills): void
    {
        $zescoBills->user_id = auth()->user()->id;
        $zescoBills->save();
    }

    /**
     * Handle the ZescoBills "updated" event.
     */
    public function updated(ZescoBills $zescoBills): void
    {
        //
    }

    /**
     * Handle the ZescoBills "deleted" event.
     */
    public function deleted(ZescoBills $zescoBills): void
    {
        //
    }

    /**
     * Handle the ZescoBills "restored" event.
     */
    public function restored(ZescoBills $zescoBills): void
    {
        //
    }

    /**
     * Handle the ZescoBills "force deleted" event.
     */
    public function forceDeleted(ZescoBills $zescoBills): void
    {
        //
    }
}
