<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitCoinToBankAccount extends Model
{
    protected $fillable = [
        'account_number',
        'amount_sats',
        'amount_btc',
        'amount_kwacha',
        'qr_code_path',
        'customer_name',
        'customer_phone',
        'delivery_email',
        'convenience_fee',
        'total_sats',
        'network_fee',
        'bank_name',
        'bank_branch',
        'bank_sort_code',
        'bank_account_type',
        'lightning_invoice_address',
        'payment_status',
        'paid_at',
        'transaction_id',
        'checking_id',
        'user_id',
        'checkout_url'
    ];
}
