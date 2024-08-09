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
        
        $topTenWinners = TrxLog::with('client')->select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId','accountId')
            ->orderBy('totalCloseProfit', 'desc')
            ->limit(10)
            ->get();
        $topTenLossers = TrxLog::with('client')->select('userId','accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId','accountId')
            ->orderBy('totalCloseProfit', 'asc')
            ->limit(10)
            ->get();;

        $activeUsers = TrxLog::whereBetween('createdDate', [$startDate, $endDate])->whereNotNull('closeProfit')->distinct('userId')->count('userId');
            
        $profitCount = TrxLog::select('userId', 'accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId', 'accountId')->havingRaw('SUM(closeProfit) > 0')->distinct('userId')->count('userId');
        $lossCount = TrxLog::select('userId', 'accountId')
            ->selectSub('SUM(closeProfit)', 'totalCloseProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->whereNotNull('closeProfit')
            ->groupBy('userId', 'accountId')->havingRaw('SUM(closeProfit) < 0')->distinct('userId')->count('userId');

        $clients = Client::with(['trxLogs' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('createdDate', [$startDate, $endDate])
                  ->whereNotNull('closeProfit');
        }])->get();

        $parentProfits = Client::join('trx_logs', 'clients.user_id', '=', 'trx_logs.userId')
            ->whereBetween('trx_logs.createdDate', [$startDate, $endDate])
            ->whereNotNull('trx_logs.closeProfit')
            ->groupBy('clients.parentId')
            ->selectRaw('clients.parentId, SUM(trx_logs.closeProfit) as totalCloseProfit')
            ->having('totalCloseProfit', '!=', 0)
            ->get();

        // Get the parent clients with aggregated profits
        $parents = Client::whereIn('user_id', $parentProfits->pluck('parentId'))
            ->get()
            ->map(function ($client) use ($parentProfits) {
                return [
                    'id' => $client->user_id,
                    'accountId' => $client->client_code,
                    'name' => $client->name,
                    'totalCloseProfit' => number_format($parentProfits->firstWhere('parentId', $client->user_id)->totalCloseProfit, 2, '.', ''),
                ];
            });

        // Sort and take top 10 winners and losers
        $topWinnerParents = $parents->sortByDesc('totalCloseProfit')->take(10);
        $topLoserParents = $parents->sortBy('totalCloseProfit')->take(10);
        
        $ids = [34, 66, 196, 649, 732, 1073, 1419, 2497, 3181, 3182, 3231, 3232, 496, 505, 516, 517];
        $specialBaseId = 517;
        $specialChildBaseId = 562;

        // Retrieve parent currencies and their child currencies in a single query
        $parentCurrencies = BaseCurrency::with(['childCurrencies' => function ($query) {
            $query->select('base_id', 'parent_id');
        }])->whereIn('base_id', $ids)->get();

        $allCurrencyIds = $parentCurrencies->flatMap(function ($parent) use ($specialBaseId, $specialChildBaseId) {
            $currencyIds = $parent->childCurrencies->pluck('base_id')->toArray();

            // Check for the special case where base_id is 517
            if ($parent->base_id == $specialBaseId) {
                $specialParent = BaseCurrency::with('childCurrencies')->find($specialChildBaseId);
                if ($specialParent) {
                    $currencyIds = array_merge($currencyIds, $specialParent->childCurrencies->pluck('base_id')->toArray());
                }
            }

            $currencyIds[] = $parent->base_id; // Include the parent currency itself
            return $currencyIds;
        })->unique();

        // Aggregate profits for all currency IDs in one query
        $profits = TrxLog::select('currencyId')
            ->selectRaw('SUM(closeProfit) as totalCloseProfit')
            ->whereIn('currencyId', $allCurrencyIds)
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->groupBy('currencyId')
            ->get()
            ->keyBy('currencyId');  // Use keyBy to create a map

        // Calculate total close profit for each parent currency
        $parentProfits = $parentCurrencies->map(function ($parent) use ($profits) {
            $totalCloseProfit = $parent->childCurrencies->pluck('base_id')
                ->merge([$parent->base_id])
                ->sum(function ($currencyId) use ($profits) {
                    // Return the totalCloseProfit or 0 if it doesn't exist
                    return optional($profits->get($currencyId))->totalCloseProfit ?? 0;
                });

            return [
                'id' => $parent->base_id,
                'name' => $parent->name,
                'totalCloseProfit' => number_format($totalCloseProfit, 2, '.', ''),
            ];
        })->toArray();

        // Sort the results by total close profit (descending)
        usort($parentProfits, function ($a, $b) {
            return $b['totalCloseProfit'] <=> $a['totalCloseProfit'];
        });

        $markets = $parentProfits;

        $top10scripts = TrxLog::with('currency')->select('currencyId')
            ->selectRaw('SUM(closeProfit) as totalCloseProfit')
            ->whereNotNull('closeProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->groupBy('currencyId')
            ->orderBy('totalCloseProfit','desc')->limit(16)->get();

        $bottom10scripts = TrxLog::with('currency')->select('currencyId')
            ->selectRaw('SUM(closeProfit) as totalCloseProfit')
            ->whereNotNull('closeProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->groupBy('currencyId')
            ->orderBy('totalCloseProfit', 'asc')->limit(16)->get();


        $commissionProfits = $clients->groupBy('user_id')->map(function ($group) {
            return $group->sum(function ($client) {
                return $client->trxLogs->sum('openCommission') + $client->trxLogs->sum('closeCommission');
            });
        })->filter(function ($sum) {
            return $sum != 0;
        });

        $commissions = Client::whereIn('user_id', $commissionProfits->keys())->get()->map(function ($client) use ($commissionProfits) {
            return [
                'id' => $client->user_id,
                'accountId' => $client->client_code,
                'name' => $client->name,
                'commissions' => number_format($commissionProfits[$client->user_id], 2, '.', ''),
            ];
        });

        $topCommissions = $commissions->sortByDesc('commissions')->take(16);

        
        if(in_array('view '.$fname,permissions())){
            return view($directory.'.index', compact('title','url','directory','date','topTenWinners','topTenLossers','topWinnerParents','topLoserParents','activeUsers','profitCount','lossCount','markets','top10scripts','bottom10scripts','topCommissions'));
        }else{
            return redirect()->route('dashboard.index');
        }
    }

    public function marketDetails(Request $request,$id){
        $title = "Market Details for `".getCurrencyName($id)."`";
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $timezone = 'Asia/Kolkata';
        $date = Carbon::today()->toDateString();
        $startDate = Carbon::now($timezone)->startOfDay()->subHours(2)->subMinutes(30);
        $endDate = Carbon::now($timezone)->endOfDay()->subHours(2)->subMinutes(30);

        $details = BaseCurrency::where('parent_id', $id)->get();
        $parentProfits = [];
        foreach ($details as $parent) {
            
            $childCurrencies = $parent->childCurrencies;
            $trxLogs = TrxLog::whereIn('currencyId', $childCurrencies->pluck('base_id'))
                 ->whereBetween('createdDate', [$startDate, $endDate])->get();
            $totalCloseProfit = $trxLogs->sum('closeProfit');
            $totalCloseProfit1 = 0;
            if($parent->base_id==517){
                $paren = BaseCurrency::where('base_id', 562)->first();
                $childCurrencies1 = $paren->childCurrencies;
                $trxLogs1 = TrxLog::whereIn('currencyId', $childCurrencies1->pluck('base_id'))
                 ->whereBetween('createdDate', [$startDate, $endDate])->get();
                $totalCloseProfit1 = $trxLogs1->sum('closeProfit');
            }
            
            $parentProfit = TrxLog::where('currencyId', $parent->base_id)
                 ->whereBetween('createdDate', [$startDate, $endDate])->sum('closeProfit');
            $totalCloseProfit = $totalCloseProfit+ $parentProfit+$totalCloseProfit1; 
            if($totalCloseProfit!=0) {  
                $parentProfits[] = [
                    'id' => $parent->base_id,
                    'name' => $parent->name,
                    'totalCloseProfit' => number_format($totalCloseProfit,'2','.',''),
                ];
            }
            
        }
        //dd($childProfits);
        // Order results by total closeProfit descending
        usort($parentProfits, function ($a, $b) {
            return $b['totalCloseProfit'] <=> $a['totalCloseProfit'];
        });
        $markets = $parentProfits;

        return view($directory.'.market-details', compact( 'title','markets','url','directory','date'));
    }

    public function scripts(Request $request){
        $title = "All Scripts";
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $timezone = 'Asia/Kolkata';
        $date = Carbon::today()->toDateString();
        $startDate = Carbon::now($timezone)->startOfDay()->subHours(2)->subMinutes(30);
        $endDate = Carbon::now($timezone)->endOfDay()->subHours(2)->subMinutes(30);

        $scripts = TrxLog::with('currency')->select('currencyId')
            ->selectRaw('SUM(closeProfit) as totalCloseProfit')
            ->whereNotNull('closeProfit')
            ->whereBetween('createdDate', [$startDate, $endDate])
            ->groupBy('currencyId')->orderBy('totalCloseProfit','desc')->get();
        
        return view($directory.'.scripts', compact( 'title','scripts','url','directory','date'));
    }

    public function moreParents(Request $request){

        $title = "All Parents";
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $timezone = 'Asia/Kolkata';
        $date = Carbon::today()->toDateString();
        $startDate = Carbon::now($timezone)->startOfDay()->subHours(2)->subMinutes(30);
        $endDate = Carbon::now($timezone)->endOfDay()->subHours(2)->subMinutes(30);

        $clients = Client::with(['trxLogs' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('createdDate', [$startDate, $endDate])
                  ->whereNotNull('closeProfit');
        }])->get();

        $parentProfits = $clients->groupBy('parentId')->map(function ($group) {
            return $group->sum(function ($client) {
                return $client->trxLogs->sum('closeProfit');
            });
        })->filter(function ($sum) {
            return $sum != 0;
        });

        $parents = Client::whereIn('user_id', $parentProfits->keys())->get()->map(function ($client) use ($parentProfits) {
            return [
                'accountId' => $client->client_code,
                'name' => $client->name,
                'totalCloseProfit' => number_format($parentProfits[$client->user_id], 2, '.', ''),
            ];
        });
        
        $scripts = $parents->sortByDesc('totalCloseProfit');
        return view($directory.'.more-parents', compact( 'title','scripts','url','directory','date'));
    }

    public function clientDetails(Request $request,$id){
        
        $title = "Client Details for `".getCurrencyName($id)."`";
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
            ->where('currencyId',$id)
            ->groupBy('userId','accountId');
        $clients = (clone $transactions)->orderBy('totalCloseProfit', 'desc')
            ->get();

        return view($directory.'.client-details', compact( 'title','clients','url','directory','date'));
    }

    public function childDetails(Request $request,$id){
        
        $title = "Child Details for `".getUserName($id)."`";
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $timezone = 'Asia/Kolkata';
        $date = Carbon::today()->toDateString();
        $startDate = Carbon::now($timezone)->startOfDay()->subHours(2)->subMinutes(30);
        $endDate = Carbon::now($timezone)->endOfDay()->subHours(2)->subMinutes(30);

        $clients = Client::with(['trxLogs' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('createdDate', [$startDate, $endDate])
                  ->whereNotNull('closeProfit');
        }])->where('parentId',$id)->get();

        $parentProfits = $clients->groupBy('user_id')->map(function ($group) {
            return $group->sum(function ($client) {
                return $client->trxLogs->sum('closeProfit');
            });
        })->filter(function ($sum) {
            return $sum != 0;
        });

        $childs = Client::whereIn('user_id', $parentProfits->keys())->get()->map(function ($client) use ($parentProfits) {
            return [
                'username' => $client->username,
                'accountId' => $client->client_code,
                'name' => $client->name,
                'totalCloseProfit' => number_format($parentProfits[$client->user_id], 2, '.', ''),
            ];
        });
        
        //$topWinnerParents = $parents->sortByDesc('totalCloseProfit')->take(10);

        return view($directory.'.child-details', compact( 'title','childs','url','directory','date'));
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
