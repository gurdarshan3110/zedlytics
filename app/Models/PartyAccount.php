<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'party_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
