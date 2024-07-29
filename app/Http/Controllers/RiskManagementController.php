<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\TrxLog;
use App\Models\Client;
use App\Models\Account;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use DataTables;

class RiskManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Risk Management';
    const URL = 'risk-management';
    const DIRECTORY = 'riskmanagement';
    const FNAME = 'risk management';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $date = Carbon::today()->toDateString();
        
        $topTenWinners = TrxLog::select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereDate('createdDate',$date)
            ->groupBy('userId')
            ->orderBy('totalCloseProfit', 'asc')
            ->limit(10)
            ->get();
        $topTenLossers = TrxLog::select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereDate('createdDate',$date)
            ->groupBy('userId')
            ->orderBy('totalCloseProfit', 'desc')
            ->limit(10)
            ->get();
        if(in_array('view '.$fname,permissions())){
            return view($directory.'.index', compact('title','url','directory','date','topTenWinners','topTenLossers'));
        }else{
            return redirect()->route('dashboard.index');
        }
    }

    public function getTrxLogs(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $topTenWinners = TrxLog::select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereDate('createdDate',$date)
            ->groupBy('userId')
            ->orderBy('totalCloseProfit', 'asc')
            ->limit(10)
            ->get();
        $topTenLosers = TrxLog::select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereDate('createdDate',$date)
            ->groupBy('userId')
            ->orderBy('totalCloseProfit', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'topTenWinners' => $topTenWinners,
            'topTenLosers' => $topTenLosers,
        ]);
    }


}
