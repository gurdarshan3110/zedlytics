<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashbookLedger extends Model
{
    use HasFactory, SoftDeletes;

    public const LEDGER_TYPE_CREDIT = 'credit';
    public const LEDGER_TYPE_CREDIT_VAL = 0;
    public const LEDGER_TYPE_DEBIT = 'debit';
    public const LEDGER_TYPE_DEBIT_VAL = 1;

    protected $table = 'cashbook_ledger';

    protected $fillable = [
        'account_code',
        'account_id',
        'bank_id',
        'utr_no',
        'amount',
        'type',
        'balance',
        'employee_id',
        'ledger_date',
        'status',
        'remarks',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
