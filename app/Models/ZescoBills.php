<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZescoBills extends Model
{
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
        'payment_status',
        'paid_at',
        'transaction_id',
        'checking_id',
        'user_id'
    ];
}
