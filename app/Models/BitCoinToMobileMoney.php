<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BitCoinToMobileMoney extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mobile_number',
        'amount_btc',
        'amount_sats',
        'amount_kwacha',
        'qr_code_path',
        'customer_name',
        'customer_phone',
        'delivery_email',
        'convenience_fee',
        'total_sats',
        'network_fee',
        'lightning_invoice_address',
        'payment_status',
        'paid_at',
        'transaction_id',
        'checking_id',
        'user_id',
        'checkout_url'
    ];


    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $query) {
            $query->where('user_id', auth()->id());
        });
    }
}
