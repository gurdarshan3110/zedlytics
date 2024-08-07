<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LedgerLog;
use Carbon\Carbon;

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
        'original_account_type',
        'bank_id',
        'utr_no',
        'transaction_id',
        'amount',
        'type',
        'employee_id',
        'ledger_date',
        'status',
        'remarks',
    ];

    protected $appends = [
        'current_balance'//,'bank_balance','balance'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($ledger) {

            LedgerLog::create([
                'cashbook_ledger_id' => $ledger->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created a new ledger entry',
            ]);
        });

        static::updating(function ($ledger) {

            $original = $ledger->getOriginal();
            $changes = [];
            foreach ($ledger->getAttributes() as $key => $value) {
                if ($original[$key] != $value) {
                    $changes[$key] = [
                        'old' => $original[$key],
                        'new' => $value
                    ];
                }
            }
            if ($changes) {
                //dd($changes);
                LedgerLog::create([
                    'cashbook_ledger_id' => $ledger->id,
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'description' => json_encode($changes),
                ]);
            }
        });

        static::deleted(function ($ledger) {
            LedgerLog::create([
                'cashbook_ledger_id' => $ledger->id,
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted the ledger entry',
            ]);
        });
    }

    public function getBankBalanceAttribute()
    {
        $balance=0;
        $ledger_date = $this->ledger_date;
        // Calculate sum of credit amounts
        $balance = self::where('bank_id', $this->bank_id)
                        ->where('ledger_date', '<=', $ledger_date)
                        ->where('id', '<=', $this->id)
                        ->sum('amount');
        return $balance;
    }

    public function getCurrentBalanceAttribute()
    {
        $balance=0;
        $user_id = Auth::user()->id;
        $ledger_date = $this->id;
        // Calculate sum of credit amounts
        $balance = self::where('bank_id', $this->bank_id)
                        ->where('id', '<=', $this->id)
                        ->sum('amount');
        $balance = number_format((float)$balance, 2, '.', '');
        return $balance;
    }


    public function getBalanceAttribute()
    {
        $balance=0;
        $ledger_date = $this->ledger_date;
        // Calculate sum of credit amounts
        $balance = self::where('ledger_date', '<=', $ledger_date)->where('id','<=',$this->id)->sum('amount');

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

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id','id');
    }

    public static function getTotalBalance()
    {
        $balance = self::whereNull('cashbook_ledger.deleted_at')->sum('amount');
        $balance = number_format((float)$balance, 2, '.', '');

        return $balance;
    }

    public static function getClosingBalance()
    {
        $yesterday = now()->subDay()->toDateString();

        $balance = self::where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereDate('ledger_date', $yesterday)
                    ->sum('amount');

        // Format the deposits to two decimal places
        $deposits = number_format((float)$deposits, 2, '.', '');
        return $deposits;
    }

    public function getBankBalance($bank_id)
    {
        $balance=0;
        $balance = self::where('bank_id', $bank_id)

                        ->sum('amount');
        return $balance;
    }

    public static function getTodaysDeposits()
    {
        $today = now()->startOfDay();
        $query = self::where('type', self::LEDGER_TYPE_CREDIT_VAL)
                    ->where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->where('ledger_date', '>=', $today);
                    //->sum('amount');
        //$deposits = number_format((float)$deposits, 2, '.', '');

        $deposit = $query->sum('amount');
        $count = $query->count();

        $formattedDeposit = number_format((float)$deposit, 2, '.', '');

        return ['deposit' => (($formattedDeposit==null)?0:$formattedDeposit), 'count' =>  (($count==null)?0:$count)];
        //return $deposits;
    }

    public static function getTodaysWithdrawals()
    {
        $today = now()->startOfDay();
        $query = self::where('type', self::LEDGER_TYPE_DEBIT_VAL)
                    ->where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->where('ledger_date', '>=', $today);

        $withdrawal = $query->sum('amount');
        $count = $query->count();

        $formattedWithdrawal = number_format((float)abs($withdrawal), 2, '.', '');

        return ['withdraw' => (($formattedWithdrawal==null)?0:$formattedWithdrawal), 'count' =>  (($count==null)?0:$count)];
    }

    public static function getEquityRecords($startDate,$endDate)
    {
        $records = EquityRecord::whereBetween('ledger_date', [$startDate, $endDate]);
        $totalDeposits = $records->sum('deposit');
        $totalWithdrawals = $records->sum('withdraw');
        $carbonEndDate = Carbon::parse($endDate);

        if ($carbonEndDate->isFuture()) {
            $currentDate = Carbon::now()->subDays(2)->toDateString();
            $records = EquityRecord::whereBetween('ledger_date', [$currentDate, $currentDate]);
            $totalEquity = $records->sum('equity');
        }else{
            $totalEquity = $records->sum('equity');
        }

        return [
            'deposit' => $totalDeposits,
            'withdraw' => $totalWithdrawals,
            'equity' => $totalEquity,
        ];
    }

    public static function getParkings($startDate,$endDate)
    {
        $amount = self::where('account_type', self::ACCOUNT_TYPE_PARTY_VAL)
                    ->whereBetween('ledger_date', [$startDate, $endDate])
                    ->where('account_code', 'like', '%PARKING%')
                    ->sum('amount');
        return (($amount>0)?number_format((-1*$amount), 2, '.', ''):number_format(abs($amount), 2, '.', ''));
    }

    public static function getDepositsBetween($startDate, $endDate)
    {
        $query = self::where('type', self::LEDGER_TYPE_CREDIT_VAL)
                        ->where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                        ->whereBetween('ledger_date', [$startDate, $endDate]);
                        //->sum('amount');
        //$deposits = number_format((float)$deposits, 2, '.', '');
        $deposit = $query->sum('amount');
        $count = $query->count();

        $formattedDeposit = number_format((float)$deposit, 2, '.', '');

        return ['deposit' => (($formattedDeposit==null)?0:$formattedDeposit), 'count' =>  (($count==null)?0:$count)];
    }

    public static function getWithdrawalsBetween($startDate, $endDate)
    {
        $query = self::where('type', self::LEDGER_TYPE_DEBIT_VAL)
                        ->where('account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                        ->whereBetween('ledger_date', [$startDate, $endDate]);
                        //->sum('amount');

        $withdrawal = $query->sum('amount');
        $count = $query->count();

        $formattedWithdrawal = number_format((float)abs($withdrawal), 2, '.', '');

        return ['withdraw' => (($formattedWithdrawal==null)?0:$formattedWithdrawal), 'count' =>  (($count==null)?0:$count)];
    }

    public static function getDataForPeriod($startDate, $endDate)
    {
        $data = self::selectRaw('banks.account_code, cashbook_ledger.type, SUM(cashbook_ledger.amount) as total')
                    ->join('banks', 'cashbook_ledger.bank_id', '=', 'banks.id')
                    ->where('cashbook_ledger.account_type', self::ACCOUNT_TYPE_CLIENT_VAL)
                    ->where('banks.status',1) 
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

    public static function getDataForBank()
    {
        $data = self::selectRaw('banks.account_code, cashbook_ledger.type, SUM(cashbook_ledger.amount) as total')
                    ->join('banks', 'cashbook_ledger.bank_id', '=', 'banks.id')
                    //->whereBetween('cashbook_ledger.ledger_date', [$startDate, $endDate])
                    ->where('banks.status',1) 
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

    public function parking($startDate,$endDate,$bank_id)
    {
        $records = self::where('brand_id',$this->id)->whereBetween('ledger_date', [$startDate, $endDate]);
        $totalDeposits = $records->sum('deposit');
        $totalWithdrawals = $records->sum('withdraw');
        $totalEquity = $records->sum('equity');

        return [
            'deposit' => $totalDeposits,
            'withdraw' => $totalWithdrawals,
            'equity' => $totalEquity,
        ];
    }

    public function party()
    {
        return $this->hasOne(Party::class, 'account_code', 'account_code');
    }

}
