<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ZescoBills extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'meter_number',
        'amount_kwacha',
        'amount_sats',
        'amount_btc',
        'phone',
        'qr_code_path',
        'lightning_invoice_address',
        'customer_name',
        'customer_phone',
        'delivery_email',
        'convenience_fee',
        'lightning_invoice_address',
        'payment_status',
        'paid_at',
        'transaction_id',
        'checking_id',
        'user_id'
    ];

     protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
        
    }
}
