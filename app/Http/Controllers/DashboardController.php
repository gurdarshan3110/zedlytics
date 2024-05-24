<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
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



        return view('dashboard.index', compact(
            'title',
            'totalBalance',
            'todaysDeposits',
            'todaysWithdrawals',
            'todayData',
            'weekData',
            'monthData',
            'bankData'
        ));
    }
}
