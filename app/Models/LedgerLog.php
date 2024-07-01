<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashbook_ledger_id',
        'user_id',
        'action',
        'description',
    ];

    public function cashbookLedger()
    {
        return $this->belongsTo(CashbookLedger::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDescriptionAttribute($value)
    {
        return json_decode($value, true);
    }
}
