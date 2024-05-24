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

    public const ACCOUNT_TYPE_CLIENT = 'Client';
    public const ACCOUNT_TYPE_CLIENT_VAL = 0;

    public const ACCOUNT_TYPE_BANK = 'Bank';
    public const ACCOUNT_TYPE_BANK_VAL = 1;

    public const ACCOUNT_TYPE_PARTY = 'Party';
    public const ACCOUNT_TYPE_PARTY_VAL = 2;

    protected $table = 'cashbook_ledger';

    protected $fillable = [
        'account_code',
        'account_type',
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
        //'current_balance','bank_balance','balance'
    ];

    public function getBankBalanceAttribute()
    {
        $balance=0;
        $ledger_date = $this->ledger_date;
        // Calculate sum of credit amounts
        $balance = self::where('bank_id', $this->bank_id)
                        ->where('ledger_date', '<=', $ledger_date)
                        ->sum('amount');
        return $balance;
    }

    public function getCurrentBalanceAttribute()
    {
        $balance=0;
        $user_id = Auth::user()->id;
        $ledger_date = $this->ledger_date;
        // Calculate sum of credit amounts
        $balance = self::where('bank_id', $this->bank_id)
                        ->where('employee_id',$user_id)
                        ->where('ledger_date', '<=', $ledger_date)
                        ->sum('amount');

        return $balance;
    }

    public function getBalanceAttribute()
    {
        $balance=0;
        $ledger_date = $this->ledger_date;
        // Calculate sum of credit amounts
        $balance = self::where('ledger_date', '<=', $ledger_date)->sum('amount');

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

    public static function getTotalBalance()
    {
        $balance = self::sum('amount');
        $balance = number_format((float)$balance, 2, '.', '');

        return $balance;
    }

    public static function getTodaysDeposits()
    {
        $today = now()->startOfDay();
        $deposits = self::where('type', self::LEDGER_TYPE_CREDIT_VAL)
                    ->where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->where('ledger_date', '>=', $today)
                    ->sum('amount');
        $deposits = number_format((float)$deposits, 2, '.', '');
        return $deposits;
    }

    public static function getTodaysWithdrawals()
    {
        $today = now()->startOfDay();
        $withdrawal = self::where('type', self::LEDGER_TYPE_DEBIT_VAL)
                    ->where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->where('ledger_date', '>=', $today)
                    ->sum('amount');

        $withdrawal = number_format((float)$withdrawal, 2, '.', '');

        return abs($withdrawal);
    }

    public static function getDataForPeriod($startDate, $endDate)
    {
        $data = self::selectRaw('banks.account_code, cashbook_ledger.type, SUM(cashbook_ledger.amount) as total')
                    ->join('banks', 'cashbook_ledger.bank_id', '=', 'banks.id')
                    ->where('cashbook_ledger.account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereBetween('cashbook_ledger.ledger_date', [$startDate, $endDate])
                    ->whereNull('cashbook_ledger.deleted_at') 
                    ->groupBy('banks.account_code', 'cashbook_ledger.type')
                    ->get();

        $result = [];

        foreach ($data as $item) {
            $accountCode = $item->account_code;
            $type = $item->type;
            $total = $item->total;

            if (!isset($result[$accountCode])) {
                $result[$accountCode] = [
                    'account_code' => $accountCode,
                    'credit' => 0,
                    'debit' => 0,
                    'balance' => 0
                ];
            }

            if ($type == \App\Models\CashbookLedger::LEDGER_TYPE_CREDIT_VAL) {
                $result[$accountCode]['credit'] += $total;
            } else {
                $result[$accountCode]['debit'] += $total;
            }

            $result[$accountCode]['balance'] = $result[$accountCode]['credit'] + $result[$accountCode]['debit'];
        }

        return array_values($result); // Convert associative array to indexed array
    }


    public function balance()
    {
        $balance=0;
        $ledger_date = $this->ledger_date;
        // Calculate sum of credit amounts
        $balance = self::where('ledger_date', '<=', $ledger_date)->sum('amount');

        $balance = number_format((float)$balance, 2, '.', '');

        return $balance;
    }

}
