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
        'phone',
        'qr_code_path',
        'bolt11',
        'lightning_invoice_address'
    ];
}
