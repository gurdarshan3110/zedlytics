<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'name',
        'description',
        'status',
    ];

    public function accounts()
    {
        return $this->hasMany(BrandAccount::class);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    public function todaysDeposits()
    {
        return $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_CREDIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereDate('cashbook_ledger.ledger_date', now()->toDateString())
                    ->sum('cashbook_ledger.amount');;
    }

    public function depositsBetween($startDate,$endDate)
    {
        return $this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_CREDIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereBetween('ledger_date', [$startDate, $endDate])
                    ->sum('cashbook_ledger.amount');;
    }

    public function todaysWithdrawals()
    {
        return number_format(abs($this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_DEBIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereDate('cashbook_ledger.ledger_date', now()->toDateString())
                    ->sum('cashbook_ledger.amount')), 2, '.', '');
    }

    public function withdrawalsBetween($startDate,$endDate)
    {
        return number_format(abs($this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.type', CashbookLedger::LEDGER_TYPE_DEBIT_VAL)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)
                    ->whereBetween('ledger_date', [$startDate, $endDate])
                    ->sum('cashbook_ledger.amount')), 2, '.', '');
    }

    public function equityRecords($startDate,$endDate)
    {
        $records = EquityRecord::where('brand_id',$this->id)->whereBetween('ledger_date', [$startDate, $endDate]);
        $totalDeposits = $records->sum('deposit');
        $totalWithdrawals = $records->sum('withdraw');
        $totalEquity = $records->sum('equity');

        return [
            'deposit' => $totalDeposits,
            'withdraw' => $totalWithdrawals,
            'equity' => $totalEquity,
        ];
    }

    public function parkings($startDate)
    {
        return number_format($this->hasManyThrough(CashbookLedger::class, Bank::class)
                    ->where('cashbook_ledger.account_type', CashbookLedger::ACCOUNT_TYPE_PARTY_VAL)
                    ->where('ledger_date', '<=', $startDate)
                    ->where('cashbook_ledger.account_code', 'like', '%PARKING%')
                    ->sum('cashbook_ledger.amount'), 2, '.', '');
    }
}
