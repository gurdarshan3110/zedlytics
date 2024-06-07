<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
use App\Models\Bank;
use App\Models\Brand;
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


        $monthStartDate = Carbon::now()->startOfMonth();
        $monthEndDate = Carbon::now()->endOfMonth();

        $monthlyDeposits = CashbookLedger::getDepositsBetween($monthStartDate, $monthEndDate);
        $monthlyWithdrawals = CashbookLedger::getWithdrawalsBetween($monthStartDate, $monthEndDate);

        $startDate = Carbon::today()->endOfDay();
        //$endDate = Carbon::tomorrow()->endOfDay();

        $brands = Brand::where('status',1)->get();
        return view('dashboard.index', compact(
            'title',
            'totalBalance',
            'todaysDeposits',
            'todaysWithdrawals',
            'yesterdayDeposits',
            'yesterdayWithdrawals',
            'monthlyDeposits',
            'monthlyWithdrawals',
            'todayData',
            'weekData',
            'monthData',
            'bankData',
            'brands',
            'startDate',
            'endDate'
        ));
    }

    public function finDetails($date){
        $date = Carbon::parse($date);
        $title = $date->format('d/m/Y').' Financial Details';
        $date = $date->toDateString();
        $banks = Bank::where('status',1)->get();
        return view('dashboard.financials', compact('title',
            'date','banks'
        ));
    }
}
