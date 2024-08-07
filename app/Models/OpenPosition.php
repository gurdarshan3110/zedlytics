<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Client;


class OpenPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticketID', 'userID', 'posCurrencyID', 'posDate', 'openAmount', 
        'closeAmount', 'posPrice', 'posType', 'openCommission', 'currentPrice', 
        'referenceCurrencyId', 'posComment', 'status'
    ];

    protected $appends = [
        'parent', 'currency_name', 'client_name'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'userID', 'user_id');
    }

    public function baseCurrency()
    {
        return $this->belongsTo(BaseCurrency::class, 'posCurrencyID', 'base_id');
    }

    // Accessor for parent
    public function getParentAttribute()
    {
        return $this->baseCurrency ? $this->baseCurrency->parent : 'N/A';
    }

    // Accessor for currency name
    public function getCurrencyNameAttribute()
    {
        return $this->baseCurrency ? $this->baseCurrency->name : 'N/A';
    }

    public function getClientNameAttribute()
    {
        return $this->client && $this->client->client_code!=2 ? $this->client->name.' ('.$this->client->client_code.')' : $this->userID;
    }

    

}
