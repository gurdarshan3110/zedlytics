<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticketID', 'userID', 'posCurrencyID', 'posDate', 'openAmount', 
        'closeAmount', 'posPrice', 'posType', 'openCommission', 'currentPrice', 
        'referenceCurrencyId', 'posComment'
    ];

    public function baseCurrency()
    {
        return $this->belongsTo(BaseCurrency::class, 'posCurrencyID');
    }
}
