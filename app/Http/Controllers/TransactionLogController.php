<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\TrxLog as Model;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class TransactionLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Transaction History';
    const URL = 'transactions';
    const DIRECTORY = 'trxlog';
    const FNAME = 'Transactions';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        //if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory'));
        // }else{
        //     return redirect()->route('dashboard.index');
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'View '.self::FNAME.' Details';
        $employee = UserClient::find($id);

        //return view(self::DIRECTORY.'.show', compact(self::DIRECTORY, 'title'));
    }

    public function list(Request $request)
    {
        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value'); // Getting search input
        $order = $request->input('order.0'); // Getting ordering input
        $columns = $request->input('columns'); // Getting column data


        // Base query with soft deletes
        $query = Model::when($search, function ($query, $search) {
                // Add your searchable columns here
                return $query->where(function ($q) use ($search) {
                    $q->where('ticketOrderId', 'like', "{$search}%")
                      ->orWhere('currencyId', 'like', "%{$search}%");
                });
            })->latest();
        //$data = Model::withTrashed()->latest()->limit(500)->get();
        $filteredRecords = $query->count();

        // Paginate the results
        $query = $query->skip($start)->take($length);

        // Get the results
        $data = $query;

        return DataTables::of($data)

            ->addColumn('ticket_id', function ($row) {
                return $row->ticketOrderId;
            })

            ->addColumn('time', function ($row) {
                return $row->ticketOrderId;
            })

            ->addColumn('action', function ($row) {
                $action = $row->trxLogActionTypeId;

                return $action;
            })

            ->addColumn('type', function ($row) {
                $type = $row->trxLogTransTypeId;

                return $type;
            })

            ->addColumn('type_detail', function ($row) {
                $trxSubTypeId = $row->trxSubTypeId;

                return $trxSubTypeId;
            })

            ->addColumn('account', function ($row) {
                $account = $row->accountId;

                return $account;
            })

            ->addColumn('parent', function ($row) {
                $parent = $row->parent;

                return $parent;
            })

            ->addColumn('amount', function ($row) {
                $amount = $row->amount;

                return $amount;
            })

            ->addColumn('script', function ($row) {
                $script = $row->currencyId;

                return $script;
            })

             ->addColumn('price', function ($row) {
                $price = $row->price;

                return $price;
            })

             ->addColumn('close_price', function ($row) {
                $closePrice = $row->closePrice;

                return $closePrice;
            })

            ->addColumn('open_commission', function ($row) {
                $open_commission = $row->openCommission;

                return $open_commission;
            })
            ->addColumn('close_commission', function ($row) {
                $close_commission = $row->closeCommission;

                return $close_commission;
            })
            ->addColumn('total_pnl', function ($row) {
                $total_pnl = $row->closePrice;

                return $total_pnl;
            })
            
        ->rawColumns(['action'])
        ->with([
                'draw' => $request->input('draw'),
                'recordsTotal' => Model::count(),
                'recordsFiltered' => $filteredRecords,
            ])
        ->make(true);
    }


}
