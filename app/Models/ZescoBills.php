<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'lightning_invoice_address'
    ];
}
