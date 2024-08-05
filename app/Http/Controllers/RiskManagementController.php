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
use App\Models\BaseCurrency;
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

    const TITLE = 'Risk Management Dashboard';
    const URL = 'risk-management';
    const DIRECTORY = 'riskmanagement';
    const FNAME = 'risk management';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $timezone = 'Asia/Kolkata';
        $date = Carbon::today()->toDateString();
        $startDate = Carbon::now($timezone)->startOfDay()->subHours(2)->subMinutes(30);
        $endDate = Carbon::now($timezone)->endOfDay()->subHours(2)->subMinutes(30);
        
        $transactions = TrxLog::with('client')->select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId','accountId');
        $topTenWinners = (clone $transactions)->orderBy('totalCloseProfit', 'desc')
            ->limit(10)
            ->get();
        $topTenLossers = (clone $transactions)->orderBy('totalCloseProfit', 'asc')
            ->limit(10)->get();

        $activeUsers = TrxLog::whereBetween('createdDate', [$startDate, $endDate])->whereNotNull('closeProfit')->distinct('userId')->count('userId');
        $transCount = TrxLog::select('userId', 'accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit');
            
        $profitCount = (clone $transCount)->groupBy('userId', 'accountId')->havingRaw('SUM(closeProfit) > 0')->distinct('userId')->count('userId');
        $lossCount = (clone $transCount)->groupBy('userId', 'accountId')->havingRaw('SUM(closeProfit) < 0')->distinct('userId')->count('userId');

        $clients = Client::with(['trxLogs' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('createdDate', [$startDate, $endDate])
                  ->whereNotNull('closeProfit');
        }])->get();

        $parentProfits = $clients->groupBy('parentId')->map(function ($group) {
            return $group->sum(function ($client) {
                return $client->trxLogs->sum('closeProfit');
            });
        });

        $parents = Client::whereIn('user_id', $parentProfits->keys())->get()->map(function ($client) use ($parentProfits) {
            return [
                'accountId' => $client->client_code,
                'name' => $client->name,
                'totalCloseProfit' => number_format($parentProfits[$client->user_id], 2, '.', ''),
            ];
        });
        
        $topWinnerParents = $parents->sortByDesc('totalCloseProfit')->take(10);
        $topLoserParents = $parents->sortBy('totalCloseProfit')->take(10);
        $ids = [34, 66, 196, 68, 649, 732, 1073, 1419, 2497, 3181, 3182, 3231, 3232, 496, 505, 516, 517];
        $parentCurrencies = BaseCurrency::whereIn('base_id', $ids)->get();

        $parentProfits = [];
        foreach ($parentCurrencies as $parent) {
            
            $childCurrencies = $parent->childCurrencies;
            $trxLogs = TrxLog::whereIn('currencyId', $childCurrencies->pluck('base_id'))
                 ->whereBetween('createdDate', [$startDate, $endDate])->get();
            $totalCloseProfit = $trxLogs->sum('closeProfit');

            $parentProfits[] = [
                'parent_id' => $parent->base_id,
                'name' => $parent->name,
                'totalCloseProfit' => $totalCloseProfit,
            ];
        }
        //dd($parentCurrencies);
        // Order results by total closeProfit descending
        usort($parentProfits, function ($a, $b) {
            return $b['totalCloseProfit'] <=> $a['totalCloseProfit'];
        });
        $markets = $parentProfits;
        //dd($markets);
        $scripts = TrxLog::with('currency')->select('currencyId')
            ->selectRaw('SUM(closeProfit) as totalCloseProfit')
            ->whereNotNull('closeProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->groupBy('currencyId');

        $top10scripts = (clone $scripts)->orderBy('totalCloseProfit','desc')->limit(10)->get();

        $bottom10scripts = (clone $scripts)->orderBy('totalCloseProfit', 'asc')->limit(10)->get();

        
        if(in_array('view '.$fname,permissions())){
            return view($directory.'.index', compact('title','url','directory','date','topTenWinners','topTenLossers','topWinnerParents','topLoserParents','activeUsers','profitCount','lossCount','markets','top10scripts','bottom10scripts'));
        }else{
            return redirect()->route('dashboard.index');
        }
    }

    public function getTrxLogs(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $timezone = 'Asia/Kolkata';

        $startDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->subHours(2)->subMinutes(30);
        $endDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->subHours(2)->subMinutes(30);

        $topTenWinners = TrxLog::with('client')->select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId','accountId')
            ->orderBy('totalCloseProfit', 'desc')
            ->limit(10)
            ->get();
        $topTenLosers = TrxLog::with('client')->select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId','accountId')
            ->orderBy('totalCloseProfit', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'topTenWinners' => $topTenWinners,
            'topTenLosers' => $topTenLosers,
            'date' => $date
        ]);
    }

    // public function moreWL(Request $request)
    // {
    //     $url = self::URL;
    //     $directory = self::DIRECTORY;
    //     $fname = self::FNAME;
    //     $status = $request->query('status');
    //     $date = $request->query('date');
    //     $title = 'Losers List';
    //     $timezone = 'Asia/Kolkata';

    //     $startDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->subHours(2)->subMinutes(30);
    //     $endDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->subHours(2)->subMinutes(30);
    //     if ($status == 'winners') {
    //         $title = 'Winners List';
    //         $data = TrxLog::with('client')->select('userId','accountId')
    //             ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
    //             ->whereBetween('createdDate', [$startDate, $endDate])
    //             ->whereNotNull('closeProfit')
    //             ->groupBy('userId','accountId')
    //             ->orderBy('totalCloseProfit', 'desc')
    //             ->get();
    //     }else{
    //         $data = TrxLog::with('client')->select('userId','accountId')
    //             ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
    //             ->whereBetween('createdDate', [$startDate, $endDate])
    //             ->whereNotNull('closeProfit')
    //             ->groupBy('userId','accountId')
    //             ->orderBy('totalCloseProfit', 'asc')
    //             ->get();
    //     }

    //     return view($directory.'.more-wl', compact('data', 'title','date','url','directory'));
    // }

    // public function moreWL()
    // {
    //     $title = self::TITLE;
    //     $url = self::URL;
    //     $directory = self::DIRECTORY;
    //     $fname = self::FNAME;
    //     $timezone = 'Asia/Kolkata';
    //     $date = Carbon::today()->toDateString();

    //     return view($directory.'.more-wl', compact('title','date','url','directory'));
    // }

    public function moreWL(Request $request)
    {
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $status = $request->query('status');
        $date = $request->query('date');
        $title = 'WL Report';
       

        return view($directory.'.more-wl', compact( 'title','date','url','directory'));
    }

    // public function list(Request $request)
    // {
    //     $url = self::URL;
    //     $directory = self::DIRECTORY;
    //     $fname = self::FNAME;
    //     $status = $request->query('status');
    //     $date = $request->query('date');
    //     $title = 'Losers List';
    //     $timezone = 'Asia/Kolkata';

    //     $startDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->subHours(2)->subMinutes(30);
    //     $endDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->subHours(2)->subMinutes(30);

    //     $length = $request->input('length', 10); // Default to 10 if not provided
    //     $start = $request->input('start', 0); // Default to 0 if not provided
    //     $search = $request->input('search.value'); 
    //     $order = $request->input('order')[0] ?? ['column' => 0, 'dir' => 'asc']; // Default order if not provided
    //     $columns = $request->input('columns', []); // Default to empty array if not provided
    //     $status = $request->input('status');

    //     $columnMap = [
    //         'client_code' => 'client_code',
    //         'username' => 'username',
    //         'parent' => 'parent',
    //         'name' => 'name',
    //         'totalCloseProfit' => 'totalCloseProfit', 
    //     ];

    //     $orderColumnIndex = $order['column'];
    //     $orderDirection = $order['dir'];
    //     $orderColumnName = $columns[$orderColumnIndex]['data'] ?? 'accountId'; // Default to 'accountId' if not provided
    //     $orderByColumn = $columnMap[$orderColumnName] ?? 'accountId'; 

    //     $query = TrxLog::with('client.parent')
    //         ->select('userId', 'accountId')
    //         ->selectRaw('SUM(closeProfit) as totalCloseProfit')
    //         ->whereBetween('createdDate', [$startDate, $endDate])
    //         ->whereNotNull('closeProfit')
    //         ->groupBy('userId', 'accountId')
    //         ->when($search, function ($query, $search) {
    //             return $query->whereHas('client', function ($q) use ($search) {
    //                 $q->where('client_code', 'like', "%{$search}%")
    //                   ->orWhere('name', 'like', "%{$search}%")
    //                   ->orWhere('username', 'like', "%{$search}%");
    //             });
    //         });

    //     // Use orderByRaw for totalCloseProfit
    //     if ($orderByColumn === 'totalCloseProfit') {
    //         $query->orderByRaw("SUM(closeProfit) $orderDirection");
    //     } else {
    //         $query->orderBy($orderByColumn, $orderDirection);
    //     }

    //     $filteredRecords = $query->count();

    //     $data = $query->skip($start)->take($length)->get();

    //     return DataTables::of($data)
    //         ->addColumn('accountId', function ($row) {
    //             return $row->accountId;
    //         })
    //         ->addColumn('username', function ($row) {
    //             return $row->client->username;
    //         })
    //         ->addColumn('parent', function ($row) {
    //             return $row->client->parent->name ?? 'N/A';
    //         })
    //         ->addColumn('name', function ($row) {
    //             return ucwords($row->client->name);
    //         })
    //         ->addColumn('totalCloseProfit', function ($row) {
    //             return $row->totalCloseProfit;
    //         })
    //         ->with([
    //             'draw' => $request->input('draw'),
    //             'recordsTotal' => TrxLog::whereBetween('createdDate', [$startDate, $endDate])
    //                 ->whereNotNull('closeProfit')
    //                 ->groupBy('userId', 'accountId')
    //                 ->count(),
    //             'recordsFiltered' => $filteredRecords,
    //         ])
    //         ->make(true);
    // }
    public function list(Request $request)
    {
        $date = $request->query('date');
        $timezone = 'Asia/Kolkata';

        $startDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->subHours(2)->subMinutes(30);
        //$startDate = '2024-07-30 00:00:00';
        $endDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->subHours(2)->subMinutes(30);

        $length = $request->input('length', 10); // Default to 10 if not provided
        $start = $request->input('start', 0); // Default to 0 if not provided
        $search = $request->input('search.value'); 
        $order = $request->input('order')[0] ?? ['column' => 0, 'dir' => 'asc']; // Default order if not provided
        $columns = $request->input('columns', []); // Default to empty array if not provided

        $columnMap = [
            'accountId' => 'accountId',
            'username' => 'username',
            'parent' => 'parent',
            'name' => 'name',
            'totalCloseProfit' => 'totalCloseProfit',
        ];

        $orderColumnIndex = $order['column'];
        $orderDirection = $order['dir'];
        $orderColumnName = $columns[$orderColumnIndex]['data'] ?? 'accountId'; // Default to 'accountId' if not provided
        $orderByColumn = $columnMap[$orderColumnName] ?? 'accountId'; 

        $query = TrxLog::with('client.parent')
            ->select('userId', 'accountId')
            ->selectRaw('SUM(closeProfit) as totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId', 'accountId')
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('client_code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            });

        if ($orderByColumn === 'totalCloseProfit') {
            $query->orderByRaw("SUM(closeProfit) $orderDirection");
        } else {
            $query->orderBy($orderByColumn, $orderDirection);
        }

        $filteredRecords = $query->count();

        $data = $query->skip($start)->take($length)->get();

        return DataTables::of($data)
            ->addColumn('accountId', function ($row) {
                return $row->accountId;
            })
            ->addColumn('username', function ($row) {
                return $row->client->username;
            })
            ->addColumn('parent', function ($row) {
                return $row->client->parent->name ?? 'N/A';
            })
            ->addColumn('name', function ($row) {
                return ucwords($row->client->name);
            })
            ->addColumn('totalCloseProfit', function ($row) {
                return $row->totalCloseProfit;
            })
            ->with([
                'draw' => $request->input('draw'),
                'recordsTotal' => TrxLog::whereBetween('createdDate', [$startDate, $endDate])
                    ->whereNotNull('closeProfit')
                    ->groupBy('userId', 'accountId')
                    ->count(),
                'recordsFiltered' => $filteredRecords,
            ])
            ->make(true);
    }


}
