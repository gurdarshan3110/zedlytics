<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\Bank;
use App\Models\CashbookLedger as Model;
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
        $ledger = Model::whereDate('ledger_date', $today)->where('employee_id',Auth::user()->id)->where('bank_id',$bankId)->orderBy('id','ASC')->get();
        $accounts = Account::where('type',Account::BANK_ACCOUNT)->get();
        return view(self::DIRECTORY.'.create', compact('title','url','directory','bankId','ledger','accounts'));
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
        $account = Account::where('account_code', $data[0])->first();
        //dd($account);
        $account_type = (($account==null)?Model::ACCOUNT_TYPE_CLIENT_VAL:(($account->type==Account::CLIENT_ACCOUNT)?Model::ACCOUNT_TYPE_CLIENT_VAL:(($account->type==Account::BANK_ACCOUNT)?Model::ACCOUNT_TYPE_BANK_VAL:Model::ACCOUNT_TYPE_PARTY_VAL)));

        $ledgerData = [
            'account_code' => $data[0],
            'account_type' => $account_type,
            'utr_no' => $data[1],
            'transaction_id' => $data[8],
            'employee_id' => Auth::user()->id,
            'ledger_date' => Carbon::now(),
            'remarks' => $data[5],
            'bank_id' => $data[9]
        ];

        // Check if either credit or debit amount is provided and set accordingly
        if (!is_null($data[2])) {
            $ledgerData['amount'] = $data[2];
            $ledgerData['type'] = Model::LEDGER_TYPE_CREDIT_VAL;
        } elseif (!is_null($data[3])) {
            $ledgerData['amount'] = -abs($data[3]);
            $ledgerData['type'] = Model::LEDGER_TYPE_DEBIT_VAL;
        }


        try {
            if($ledgerData['amount']!=null){
                Model::updateOrCreate(
                    ['transaction_id' => $data[8]],
                    $ledgerData
                );
            }
            return response()->json(['success' => true, 'message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error saving data: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request,Model $ledger)
    {
        $input = $request->all();
        $ledger->remarks = $input['remarks'];
        $ledger->save();
        $ledger->delete();

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' deleted successfully.');
    }

    public function list()
    {
        $data = Model::withTrashed()->latest()->get();

        return DataTables::of($data)

            ->addColumn('id', function ($row) {
                return $row->id;
            })

            ->addColumn('bank', function ($row) {
                return $row->bank->account_code;
            })

            ->addColumn('account_code', function ($row) {
                $account_code = $row->account_code;

                return $account_code;
            })

            ->addColumn('utr_no', function ($row) {
                $utr_no = $row->utr_no;

                return $utr_no;
            })

            ->addColumn('credit', function ($row) {
                $credit = (($row->type==Model::LEDGER_TYPE_CREDIT_VAL)?$row->amount:'');

                return $credit;
            })

            ->addColumn('debit', function ($row) {
                $debit = (($row->type==Model::LEDGER_TYPE_DEBIT_VAL)?abs($row->amount):'');

                return $debit;
            })

            ->addColumn('balance', function ($row) {
                return $row->balance;
            })

            ->addColumn('ledger_date', function ($row) {
                if($row->trashed()){
                    $date = Carbon::parse($row->deleted_at);
                }else{
                    $date = Carbon::parse($row->ledger_date);
                };
                $formattedDate = $date->format('d/m/y h:i:s A');
                return $formattedDate;
            })

            ->addColumn('deleted', function ($row) {
                $deleted = $row->trashed();
                return $deleted;
            })

            ->addColumn('action', function ($row) {
                $msg = 'Are you sure ! Please enter remarks?';
                $action = '';
                if (!$row->trashed()) {
                    $action = '<form id="deleteForm'.$row->id.'" action="'.route(self::URL.'.destroy', [$row]).'" method="post">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <input type="hidden" id="remarks'.$row->id.'" name="remarks" value="">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete('.$row->id.')"><i class="far fa-trash-alt"></i></button>
                                </div>
                            </form>';
                }else{
                    $action = $row->remarks;
                }

                return $action;
            })


        ->rawColumns(['action'])
        ->make(true);
    }


}
