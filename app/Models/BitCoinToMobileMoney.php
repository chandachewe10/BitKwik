<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitCoinToMobileMoney extends Model
{
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
}
