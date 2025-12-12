<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileToBitcoin extends Model
{
    protected $fillable = [
        'user_id',
        'checking_id',
        'phone_number',
        'amount_sats',
        'amount_btc',
        'amount_kwacha',
        'external_id',
        'callback_url',
        'total_sats',
        'network_fee',
        'convenience_fee',
        'payment_status',
        'uri',
        'lightning_invoice_address',
        'qr_code_path',
        'lnurl',
        'paid_at',
        'is_used',
    ];
}
