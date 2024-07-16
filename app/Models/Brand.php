<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'name',
        'description',
        'username',
        'password',
        'company_name',
        'status',
    ];

    public function accounts()
    {
        return $this->hasMany(BrandAccount::class)->where('status', 1);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class)->where('status', 1);
    }

    public function todaysDeposits()
    {
        $query = $this->hasManyThrough(CashbookLedger::class, Bank::class)
                      ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_CREDIT_VAL)
                      ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                      ->whereDate('cashbook_ledger.ledger_date', now()->toDateString());

        $deposit = $query->sum('cashbook_ledger.amount');
        $count = $query->count();

        $formattedDeposit = number_format((float)$deposit, 2, '.', '');

        return ['deposit' => (($formattedDeposit==null)?0:$formattedDeposit), 'count' =>  (($count==null)?0:$count)];
    }

    public function brandBalance()
    {
        $query = $this->hasManyThrough(CashbookLedger::class, Bank::class);

        $deposit = number_format(abs($query->sum('cashbook_ledger.amount')), 2, '.', '');

        return $deposit;

    }

    public function depositsBetween($startDate,$endDate)
    {
        $query = $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_CREDIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereBetween('ledger_date', [$startDate, $endDate]);

        $deposit = number_format(abs($query->sum('cashbook_ledger.amount')), 2, '.', '');
        $count = $query->count();

        return ['deposit' => $deposit, 'count' => $count];

    }

    public function todaysWithdrawals()
    {
        $query = $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_DEBIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereDate('cashbook_ledger.ledger_date', now()->toDateString());
        $withdraw = number_format(abs($query->sum('cashbook_ledger.amount')), 2, '.', '');
        $count = $query->count();

        return ['withdraw' => $withdraw, 'count' => $count];
    }

    public function withdrawalsBetween($startDate,$endDate)
    {
        $query = $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_DEBIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereBetween('ledger_date', [$startDate, $endDate]);

        $withdraws = number_format(abs($query->sum('cashbook_ledger.amount')), 2, '.', '');
        $count = $query->count();

        return ['withdraw' => $withdraws, 'count' => $count];

    }

    public function equityRecords($startDate,$endDate)
    {
        $records = EquityRecord::where('brand_id',$this->id)->whereBetween('ledger_date', [$startDate, $endDate]);
        $totalDeposits = $records->sum('deposit');
        $totalWithdrawals = $records->sum('withdraw');
        $carbonEndDate = Carbon::parse($endDate);

        if ($carbonEndDate->isFuture()) {
            $currentDate = Carbon::now()->subDays(2)->toDateString();
            $records = EquityRecord::where('brand_id',$this->id)->whereBetween('ledger_date', [$currentDate, $currentDate]);
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

    public function parkings($startDate)
    {
        $amount = $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_PARTY_VAL)
                    ->where('ledger_date', '=', $startDate)
                    ->where('cashbook_ledger.account_code', 'like', '%PARKING%')
                    ->sum('cashbook_ledger.amount');
        return (($amount>0)?number_format((-1*$amount), 2, '.', ''):number_format(abs($amount), 2, '.', ''));
    }
    public function parkingsupto($startDate,$endDate)
    {
        $amount = $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_PARTY_VAL)
                    ->whereBetween('ledger_date', [$startDate, $endDate])
                    ->where('cashbook_ledger.account_code', 'like', '%PARKING%')
                    ->sum('cashbook_ledger.amount');
        return (($amount>0)?number_format((-1*$amount), 2, '.', ''):number_format(abs($amount), 2, '.', ''));
    }
}
