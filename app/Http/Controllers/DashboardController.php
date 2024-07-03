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
use Illuminate\Support\Collection;

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
                ->map(function (Collection $group) {
                    // Get all but the last entry
                    $allButLast = $group->slice(0, -1);

                    // Calculate metrics for all but the last entry
                    $longQty = $allButLast->where('posType', 1)->sum('openAmount');
                    $shortQty = $allButLast->where('posType', 2)->sum('openAmount');
                    $longDeals = $allButLast->where('posType', 1)->count();
                    $shortDeals = $allButLast->where('posType', 2)->count();
                    $netQty = round($longQty, 2) - round($shortQty, 2);

                    // Calculate metrics for the last entry
                    $lastEntry = $group->last();
                    $lastLongQty = $lastEntry->posType == 1 ? $lastEntry->openAmount : 0;
                    $lastShortQty = $lastEntry->posType == 2 ? $lastEntry->openAmount : 0;

                    $firstPosition = $group->first();

                    return [
                        'parent' => $firstPosition->baseCurrency->parent,
                        'currency_name' => $firstPosition->baseCurrency->name,
                        'longDeals' => $longDeals,
                        'longQty' => $longQty,
                        'shortDeals' => $shortDeals,
                        'shortQty' => $shortQty,
                        'netQty' => $netQty,
                        'lastChange' => $lastEntry->updated_at,
                        'previousNetQty' => round($netQty, 2),
                    ];
                });

            // Paginate the results manually since we are using collections
            // $perPage = 10;
            // $page = request()->get('page', 1);
            // $total = $positions->count();
            // $results = $positions->slice(($page - 1) * $perPage, $perPage)->values();
            // $positions = new \Illuminate\Pagination\LengthAwarePaginator($results, $total, $perPage, $page, [
            //     'path' => request()->url(),
            //     'query' => request()->query(),
            // ]);
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
