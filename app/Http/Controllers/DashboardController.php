<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
use App\Models\Bank;
use App\Models\Brand;
use Carbon\Carbon;
use App\Models\WithdrawRequest;
use App\Models\OpenPosition;
use App\Models\CronJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    const TITLE = 'Dashboard';

    public function index()
    {
        $title = self::TITLE;
        $permissions = permissions();
        if(Auth::user()->role=='Margin And Limits Menu'){
            return view('dashboard.margin-limit-menu',compact('title'));
        }
        if(in_array('employee dashboard',$permissions)){
            return redirect()->intended('/employee-dashboard');
        }
        
        $totalBalance = CashbookLedger::getTotalBalance();
        $todaysDeposits = CashbookLedger::getTodaysDeposits();
        $todaysWithdrawals = CashbookLedger::getTodaysWithdrawals();
        $todaysParkings = CashbookLedger::getParkings(now()->startOfDay()->toDateString(),now()->endOfDay()->toDateString());
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
        $yesterdaysParkings = CashbookLedger::getParkings($yesterdayStartDate->toDateString(),$yesterdayEndDate->toDateString());
        $yesterdaysEquity = CashbookLedger::getEquityRecords($yesterdayEndDate->toDateString(),$yesterdayEndDate->toDateString());

        $dayBeforeYesterdayStartDate = Carbon::yesterday()->subDay()->startOfDay();
        $dayBeforeYesterdayEndDate = Carbon::yesterday()->subDay()->endOfDay();

        $dayBefYesDeposits = CashbookLedger::getDepositsBetween($dayBeforeYesterdayStartDate, $dayBeforeYesterdayEndDate);
        $dayBefYesWithdrawals = CashbookLedger::getWithdrawalsBetween($dayBeforeYesterdayStartDate, $dayBeforeYesterdayEndDate);
        $dayBefYesParkings = CashbookLedger::getParkings($dayBeforeYesterdayStartDate->toDateString(),$dayBeforeYesterdayEndDate->toDateString());
        $dayBefYesEquity = CashbookLedger::getEquityRecords($dayBeforeYesterdayEndDate->toDateString(),$dayBeforeYesterdayEndDate->toDateString());


        $monthStartDate = Carbon::now()->startOfMonth();
        $monthEndDate = Carbon::now()->endOfMonth();

        $monthlyDeposits = CashbookLedger::getDepositsBetween($monthStartDate, $monthEndDate);
        $monthlyWithdrawals = CashbookLedger::getWithdrawalsBetween($monthStartDate, $monthEndDate);
        $monthlyParkings = CashbookLedger::getParkings($monthStartDate->toDateString(),$monthEndDate->toDateString());
        $monthlyEquity = CashbookLedger::getEquityRecords($monthStartDate->toDateString(),$monthEndDate->toDateString());

        $startDate = Carbon::today()->endOfDay();
        //$endDate = Carbon::tomorrow()->endOfDay();

        $monthBeforeStartDate = Carbon::now()->subMonth()->startOfMonth();
        $monthBeforeEndDate = Carbon::now()->subMonth()->endOfMonth();

        $yesMonthlyDeposits = CashbookLedger::getDepositsBetween($monthBeforeStartDate, $monthBeforeEndDate);
        $yesMonthlyWithdrawals = CashbookLedger::getWithdrawalsBetween($monthBeforeStartDate, $monthBeforeEndDate);
        $yesMonthlyParkings = CashbookLedger::getParkings($monthBeforeStartDate->toDateString(),$monthBeforeEndDate->toDateString());
        $yesMonthlyEquity = CashbookLedger::getEquityRecords($monthBeforeStartDate->toDateString(),$monthBeforeEndDate->toDateString());



        $brands = Brand::where('status',1)->get();
        $withdrawRequests = WithdrawRequest::where('status',0)->sum('amount');
        $positions = [];
        if(Auth::user()->role=='Partner'){
            $lastCronJob = CronJob::where('cron_job_name','Open Position')->latest()->skip(1)->first();  
            $lastCronJobTime = null;

            if ($lastCronJob) {
                // Create a DateTime object from the hit_time in Indian time (assuming the stored time is in Indian Standard Time)
                $dateTimeIndian = new \DateTime($lastCronJob->created_at, new \DateTimeZone('Asia/Kolkata'));

                // Convert the time to Saudi time
                $dateTimeIndian->setTimezone(new \DateTimeZone('Asia/Riyadh'));

                // Format the DateTime object to a string (or you can use the DateTime object directly)
                $lastCronJobTime = $dateTimeIndian->format('Y-m-d H:i:s');
            }
            $positions = OpenPosition::with('baseCurrency')
                ->get()
                ->groupBy('posCurrencyID')
                ->map(function (Collection $group) use ($lastCronJobTime) {
                    // Exclude the last entry
                    $allButLast = $group;

                    // Calculate metrics for all but the last entry
                    //$longQty = $allButLast->where('posType', 1)->sum('openAmount') - $allButLast->where('posType', 1)->sum('closeAmount');
                    $longQty = $allButLast->where('posType', 1)->sum('openAmount');
                    //$shortQty = $allButLast->where('posType', 2)->sum('openAmount') + $allButLast->where('posType', 2)->sum('closeAmount');
                    $shortQty = $allButLast->where('posType', 2)->sum('openAmount');
                    
                    $longDeals = $allButLast->where('posType', 1)->count();
                    $shortDeals = $allButLast->where('posType', 2)->count();
                    $netQty = $longQty + $shortQty;

                    // Calculate changeQty if the second-to-last cron job time is available
                    $changeQty = 0;
                    if ($lastCronJobTime) {
                        $changeQty = $allButLast
                            ->where('posDate', '>', $lastCronJobTime)
                            ->sum('openAmount');
                    }

                    $firstPosition = $group->first();
                    $lastEntry = $group->last();

                    return [
                        'parent' => $firstPosition->baseCurrency ? $firstPosition->baseCurrency->parent : 'N/A',
                        'currency_name' => $firstPosition->baseCurrency ? $firstPosition->baseCurrency->name : 'N/A',
                        'currency_id' => $firstPosition->posCurrencyID,
                        'longDeals' => $longDeals,
                        'longQty' => $longQty,
                        'shortDeals' => $shortDeals,
                        'shortQty' => abs($shortQty),
                        'netQty' => round($netQty,2),
                        'lastChange' => $lastEntry->updated_at,
                        'changeQty' => $changeQty,
                    ];
                })
                ->sortByDesc('netQty')
                ->values()
                ->all();

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
            'dayBefYesDeposits',
            'dayBefYesWithdrawals',
            'dayBefYesParkings',
            'dayBefYesEquity',
            'yesMonthlyDeposits',
            'yesMonthlyWithdrawals',
            'yesMonthlyParkings',
            'yesMonthlyEquity',
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
            'positions',
            'dayBeforeYesterdayStartDate',
            'dayBeforeYesterdayEndDate',
            'monthBeforeStartDate',
            'monthBeforeEndDate',
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
        $dayBeforeYesterdayStartDate = Carbon::yesterday()->subDay()->startOfDay();
        $dayBeforeYesterdayEndDate = Carbon::yesterday()->subDay()->endOfDay();
        $monthBeforeStartDate = Carbon::now()->subMonth()->startOfMonth();
        $monthBeforeEndDate = Carbon::now()->subMonth()->endOfMonth();
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
            'dayBeforeYesterdayStartDate',
            'dayBeforeYesterdayEndDate',
            'monthBeforeStartDate',
            'monthBeforeEndDate',
        ));
    }

    public function finDetails($date,$brand){
        $date = Carbon::parse($date);
        $title = $date->format('d/m/Y').' Financial Details';
        $date = $date->toDateString();
        if($brand=='all'){
            $banks = Bank::where('status',1)->get();
            return view('dashboard.financials', compact('title',
                'date','banks','brand'
            ));
        }else{
            $banks = Bank::where('status',1)->where('brand_id',$brand)->get();
            return view('dashboard.financials', compact('title',
                'date','banks','brand'
            ));
        }
    }
    public function segregatePositions($id){
        $positionName = OpenPosition::with('baseCurrency')->where('posCurrencyID', $id)->first();
        $title = ' Segregate Values for '.$positionName->baseCurrency->name;

        $positions = OpenPosition::where('posCurrencyID', $id)->where('status',0)->get(); 
        $posType1 = [];
        $posType2 = [];

        foreach ($positions as $position) {
            if ($position->posType == 1) {
                $posType1[] = $position;
            } elseif ($position->posType == 2) {
                $posType2[] = $position;
            }
        }

        $long=$posType1;
        $short=$posType2;
        return view('dashboard.segregatepositions', compact(
            'title',
            'long',
            'short',
        ));
    }
}
