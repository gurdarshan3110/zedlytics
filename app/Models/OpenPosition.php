<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticketID', 'userID', 'posCurrencyID', 'posDate', 'openAmount', 
        'closeAmount', 'posPrice', 'posType', 'openCommission', 'currentPrice', 
        'referenceCurrencyId', 'posComment', 'status'
    ];

    protected $appends = [
        'parent', 'currency_name'
    ];

    public function baseCurrency()
    {
        return $this->belongsTo(BaseCurrency::class, 'posCurrencyID', 'base_id');
    }

    // Accessor for parent
    public function getParentAttribute()
    {
        return $this->baseCurrency ? $this->baseCurrency->parent : null;
    }

    // Accessor for currency name
    public function getCurrencyNameAttribute()
    {
        return $this->baseCurrency ? $this->baseCurrency->name : null;
    }
}
