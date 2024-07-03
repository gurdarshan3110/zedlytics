<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
use App\Models\Bank;
use App\Models\Brand;
use Carbon\Carbon;
use App\Models\WithdrawRequest;
use App\Models\OpenPosition;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    const TITLE = 'Dashboard';

    public function index()
    {
        $permissions = permissions();
        if(in_array('employee dashboard',$permissions)){
            return redirect()->intended('/employee-dashboard');
        }
        $title = self::TITLE;
        $totalBalance = CashbookLedger::getTotalBalance();
        $todaysDeposits = CashbookLedger::getTodaysDeposits();
        $todaysWithdrawals = CashbookLedger::getTodaysWithdrawals();
        $todaysParkings = CashbookLedger::getParkings(now()->startOfDay()->toDateString());
        $todaysEquity = CashbookLedger::getEquityRecords(now()->startOfDay()->toDateString(),now()->endOfDay()->toDateString());

        $todayData = CashbookLedger::getDataForPeriod(now()->startOfDay(), now()->endOfDay());
        $weekData = CashbookLedger::getDataForPeriod(now()->startOfWeek(), now()->endOfWeek());
        $monthData = CashbookLedger::getDataForPeriod(now()->startOfMonth(), now()->endOfMonth());
        $bankData = CashbookLedger::getDataForBank();

        $yesterdayStartDate = Carbon::yesterday()->startOfDay();
        $yesterdayEndDate = Carbon::yesterday()->endOfDay();
        $endDate = Carbon::yesterday()->endOfDay();
        
        $yesterdayDeposits = CashbookLedger::getDepositsBetween($yesterdayStartDate, $yesterdayEndDate);
        $yesterdayWithdrawals = CashbookLedger::getWithdrawalsBetween($yesterdayStartDate, $yesterdayEndDate);
        $yesterdaysParkings = CashbookLedger::getParkings($yesterdayEndDate->toDateString());
        $yesterdaysEquity = CashbookLedger::getEquityRecords($yesterdayEndDate->toDateString(),$yesterdayEndDate->toDateString());


        $monthStartDate = Carbon::now()->startOfMonth();
        $monthEndDate = Carbon::now()->endOfMonth();

        $monthlyDeposits = CashbookLedger::getDepositsBetween($monthStartDate, $monthEndDate);
        $monthlyWithdrawals = CashbookLedger::getWithdrawalsBetween($monthStartDate, $monthEndDate);
        $monthlyParkings = CashbookLedger::getParkings($monthEndDate->toDateString());
        $monthlyEquity = CashbookLedger::getEquityRecords($monthStartDate->toDateString(),$monthEndDate->toDateString());

        $startDate = Carbon::today()->endOfDay();
        //$endDate = Carbon::tomorrow()->endOfDay();

        $brands = Brand::where('status',1)->get();
        $withdrawRequests = WithdrawRequest::where('status',0)->sum('amount');
        $positions = [];
        if(Auth::user()->role=='Partner'){
            $positions = OpenPosition::with('baseCurrency')
                ->get()
                ->groupBy('posCurrencyID')
                ->map(function ($group) {
                    $longQty = $group->where('posType', 1)->sum('openAmount');
                    $shortQty = $group->where('posType', 2)->sum('openAmount');
                    $longDeals = $group->where('posType', 1)->count();
                    $shortDeals = $group->where('posType', 2)->count();
                    $netQty = $longQty .'-'. $shortQty.' = '.$longQty - $shortQty;

                    $firstPosition = $group->first();

                    return [
                        'parent' => $firstPosition->baseCurrency->parent,
                        'currency_name' => $firstPosition->baseCurrency->name,
                        'longDeals' => $longDeals,
                        'longQty' => $longQty,
                        'shortDeals' => $shortDeals,
                        'shortQty' => $shortQty,
                        'netQty' => $netQty,
                        'lastChange' => $firstPosition->updated_at,
                    ];
                });
        }
        return view('dashboard.index', compact(
            'title',
            'totalBalance',
            'todaysDeposits',
            'todaysWithdrawals',
            'todaysParkings',
            'todaysEquity',
            'yesterdayDeposits',
            'yesterdayWithdrawals',
            'yesterdaysParkings',
            'yesterdaysEquity',
            'monthlyDeposits',
            'monthlyWithdrawals',
            'monthlyParkings',
            'monthlyEquity',
            'todayData',
            'weekData',
            'monthData',
            'bankData',
            'brands',
            'startDate',
            'endDate',
            'yesterdayStartDate',
            'yesterdayEndDate',
            'monthStartDate',
            'monthEndDate',
            'withdrawRequests',
            'positions'
        ));
    }

    public function dashboard()
    {
        $permissions = permissions();
        if(in_array('dashboard',$permissions)){
            return redirect()->intended('/dashboard');
        }
        $title = self::TITLE;
        $totalBalance = CashbookLedger::getTotalBalance();
        $todaysDeposits = CashbookLedger::getTodaysDeposits();
        $todaysWithdrawals = CashbookLedger::getTodaysWithdrawals();

        $todayData = CashbookLedger::getDataForPeriod(now()->startOfDay(), now()->endOfDay());
        $weekData = CashbookLedger::getDataForPeriod(now()->startOfWeek(), now()->endOfWeek());
        $monthData = CashbookLedger::getDataForPeriod(now()->startOfMonth(), now()->endOfMonth());
        $bankData = CashbookLedger::getDataForBank();

        $yesterdayStartDate = Carbon::yesterday()->startOfDay();
        $yesterdayEndDate = Carbon::yesterday()->endOfDay();
        $endDate = Carbon::yesterday()->endOfDay();
        
        $yesterdayDeposits = CashbookLedger::getDepositsBetween($yesterdayStartDate, $yesterdayEndDate);
        $yesterdayWithdrawals = CashbookLedger::getWithdrawalsBetween($yesterdayStartDate, $yesterdayEndDate);


        $monthStartDate = Carbon::now()->startOfMonth();
        $monthEndDate = Carbon::now()->endOfMonth();

        $monthlyDeposits = CashbookLedger::getDepositsBetween($monthStartDate, $monthEndDate);
        $monthlyWithdrawals = CashbookLedger::getWithdrawalsBetween($monthStartDate, $monthEndDate);

        $startDate = Carbon::today()->endOfDay();
        //$endDate = Carbon::tomorrow()->endOfDay();

        $brands = Brand::where('status',1)->get();
        return view('dashboard.dashboard', compact(
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
            'endDate',
            'yesterdayStartDate',
            'yesterdayEndDate',
            'monthStartDate',
            'monthEndDate',
        ));
    }

    public function finDetails($date,$brand){
        $date = Carbon::parse($date);
        $title = $date->format('d/m/Y').' Financial Details';
        $date = $date->toDateString();
        if($brand=='all'){
            $banks = Bank::where('status',1)->get();
            return view('dashboard.financials', compact('title',
                'date','banks'
            ));
        }else{
            $banks = Bank::where('status',1)->where('brand_id',$brand)->get();
            return view('dashboard.financials', compact('title',
                'date','banks','brand'
            ));
        }
    }
}
