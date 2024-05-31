<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
use App\Models\Bank;
use Carbon\Carbon;

class DashboardController extends Controller
{
    const TITLE = 'Dashboard';

    public function index()
    {
        $title = self::TITLE;
        $totalBalance = CashbookLedger::getTotalBalance();
        $todaysDeposits = CashbookLedger::getTodaysDeposits();
        $todaysWithdrawals = CashbookLedger::getTodaysWithdrawals();

        $todayData = CashbookLedger::getDataForPeriod(now()->startOfDay(), now()->endOfDay());
        $weekData = CashbookLedger::getDataForPeriod(now()->startOfWeek(), now()->endOfWeek());
        $monthData = CashbookLedger::getDataForPeriod(now()->startOfMonth(), now()->endOfMonth());
        $bankData = CashbookLedger::getDataForBank();

        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::yesterday()->endOfDay();
        
        $yesterdayDeposits = CashbookLedger::getDepositsBetween($startDate, $endDate);
        $yesterdayWithdrawals = CashbookLedger::getWithdrawalsBetween($startDate, $endDate);


        $banks = Bank::where('status',1)->get();
        return view('dashboard.index', compact(
            'title',
            'totalBalance',
            'todaysDeposits',
            'todaysWithdrawals',
            'yesterdayDeposits',
            'yesterdayWithdrawals',
            'todayData',
            'weekData',
            'monthData',
            'bankData',
            'banks'
        ));
    }
}
