<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Party as Model;
use App\Models\Account;
use App\Models\Bank;
use App\Models\CashbookLedger;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Ledger';
    const URL = 'ledger';
    const DIRECTORY = 'ledger';
    const FNAME = 'Ledger';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        if(in_array('view '.$url,permissions())){
            $accounts = Account::where('type',Account::BANK_ACCOUNT)->get();
            return view($directory.'.index', compact('title','url','directory','accounts'));
        }else{
            return redirect()->route('dashboard.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $title = 'Add New '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $account = Account::findOrFail($id);
        $bankId = $account->bankAccounts->first()->bank_id;
        $today = Carbon::today()->toDateString();
        $ledger = CashbookLedger::whereDate('ledger_date', $today)->where('employee_id',Auth::user()->id)->where('bank_id',$bankId)->get();
        return view(self::DIRECTORY.'.create', compact('title','url','directory','bankId','ledger'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validatedData = $request->validate([
            'name' => 'required',
            'account_code' => 'required|unique:accounts',
            'status' => 'required'
        ]);

        $input['type'] = Account::PARTY_ACCOUNT;
        $party = Model::create($input);
        $account = Account::create($input);
        $account->banks()->attach($party);

        return redirect()->route(self::URL.'.index', $party->id)->with('success', self::FNAME.' created successfully.');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Model $party)
    {
        $title = 'Edit '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        return view(self::DIRECTORY.'.edit', compact(self::DIRECTORY, 'title','directory','url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Model $party)
    {
        $input = $request->all();
        $account = Account::where('account_code', $party->account_code)->first();
        $validatedData = $request->validate([
            'name' => 'required',
            'account_code' => 'required|unique:accounts',
            'status' => 'required'
        ]);
        $party->update($input);
        $account->update(['name' => $request->name,'account_no' => $request->account_no]);

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' updated successfully.');
    }

    public function saveLedger(Request $request)
    {
        $data = $request->all();
        $accountId = Account::where('account_code', $data[0])->value('id');

        $ledgerData = [
            'account_code' => $data[0],
            'account_id' => $accountId,
            'utr_no' => $data[1],
            'employee_id' => Auth::user()->id,
            'ledger_date' => Carbon::now(),
            'remarks' => $data[8],
            'bank_id' => $data[9]
        ];

        // Check if either credit or debit amount is provided and set accordingly
        if (!is_null($data[2])) {
            $ledgerData['amount'] = $data[2];
            $ledgerData['type'] = CashbookLedger::LEDGER_TYPE_CREDIT_VAL;
        } elseif (!is_null($data[3])) {
            $ledgerData['amount'] = $data[3];
            $ledgerData['type'] = CashbookLedger::LEDGER_TYPE_DEBIT_VAL;
        }

        // Balance should be set only if provided
        if (!is_null($data[4])) {
            $ledgerData['balance'] = $data[4];
        }

        try {
            if($ledgerData['amount']!=null){
                CashbookLedger::updateOrCreate(
                    ['account_code' => $data[0], 'utr_no' => $data[1]],
                    $ledgerData
                );
            }
            return response()->json(['success' => true, 'message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error saving data: ' . $e->getMessage()]);
        }
    }


    public function list()
    {
        $data = Model::latest()->get();

        return DataTables::of($data)


            ->addColumn('name', function ($row) {
                $name = $row->name;

                return $name;
            })

            ->addColumn('account_code', function ($row) {
                $account_code = $row->account_code;

                return $account_code;
            })

            ->addColumn('status', function ($row) {
                $status = (($row->status == 1) ? 'Active' : 'Inactive');

                return $status;
            })
            ->addColumn('action', function ($row) {
                $msg = 'Are you sure?';
                $action = '<form action="'.route(self::URL.'.destroy', [$row]).'" method="post">
                    '.csrf_field().'
                    '.method_field('DELETE').'
                    <div class="btn-group">
                    '.((in_array('edit '.self::DIRECTORY, permissions()))?'
                    <a href="'.route(self::URL.'.edit', [$row]).'"
                       class="btn btn-warning btn-xs">
                        <i class="far fa-edit"></i>
                    </a>':'').((in_array('delete '.self::DIRECTORY, permissions()))?'
                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\''.$msg.'\')"><i class="far fa-trash-alt"></i></button>':'').'
                    
                </div>
                </form>';

                return $action;
            })
        ->rawColumns(['action'])
        ->make(true);
    }


}
