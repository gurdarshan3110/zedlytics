<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        'bank_id',
        'utr_no',
        'amount',
        'type',
        'employee_id',
        'ledger_date',
        'status',
        'remarks',
    ];

    protected $appends = [
        'current_balance','bank_balance','balance'
    ];

    public function getBankBalanceAttribute()
    {
        $balance=0;
        // Calculate sum of credit amounts
        $creditSum = $this->where('bank_id', $this->bank_id)
                           ->where('type', self::LEDGER_TYPE_CREDIT_VAL)
                           ->where('ledger_date', '<=', $this->ledger_date)
                           ->sum('amount');

        // Calculate sum of debit amounts
        $debitSum = $this->where('bank_id', $this->bank_id)
                          ->where('type', self::LEDGER_TYPE_DEBIT_VAL)
                          ->where('ledger_date', '<=', $this->ledger_date)
                          ->sum('amount');

        // Calculate balance
        $balance = $creditSum + $debitSum;

        return $balance;
    }

    public function getCurrentBalanceAttribute()
    {
        $balance=0;
        $user_id = Auth::user()->id;
        // Calculate sum of credit amounts
        $creditSum = $this->where('bank_id', $this->bank_id)
                        ->where('employee_id',$user_id)
                           ->where('type', self::LEDGER_TYPE_CREDIT_VAL)
                           ->where('ledger_date', '<=', $this->ledger_date)
                           ->sum('amount');

        // Calculate sum of debit amounts
        $debitSum = $this->where('bank_id', $this->bank_id)
                        ->where('employee_id',$user_id)
                          ->where('type', self::LEDGER_TYPE_DEBIT_VAL)
                          ->where('ledger_date', '<=', $this->ledger_date)
                          ->sum('amount');

        // Calculate balance
        $balance = $creditSum + $debitSum;

        return $balance;
    }

    public function getBalanceAttribute()
    {
        $balance=0;
        // Calculate sum of credit amounts
        $creditSum = $this->where('type', self::LEDGER_TYPE_CREDIT_VAL)
                           ->where('ledger_date', '<=', $this->ledger_date)
                           ->sum('amount');

        // Calculate sum of debit amounts
        $debitSum = $this->where('type', self::LEDGER_TYPE_DEBIT_VAL)
                          ->where('ledger_date', '<=', $this->ledger_date)
                          ->sum('amount');

        // Calculate balance
        $balance = $creditSum + $debitSum;

        return $balance;
    }

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
