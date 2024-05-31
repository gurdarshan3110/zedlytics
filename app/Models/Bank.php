<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'name',
        'account_no',
        'description',
        'status',
        'rm',
    ];

    public function accounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function bankBalance()
    {
        $balance=0;
        $balance = CashbookLedger::where('bank_id', $this->id)
                        ->sum('amount');
        return $balance;
    }
}
