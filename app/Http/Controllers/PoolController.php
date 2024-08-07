<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Party;
use App\Models\CashbookLedger as Model;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class PoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Pool';
    const URL = 'pool';
    const DIRECTORY = 'pool';
    const FNAME = 'Pool';

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
        $ledger = Model::whereDate('ledger_date', $today)->where('bank_id',$bankId)->where('account_type', Model::ACCOUNT_TYPE_PARTY_VAL)->orderBy('id','ASC')->get();
        $accounts = Account::where('type',Account::BANK_ACCOUNT)->get();
        return view(self::DIRECTORY.'.create', compact('title','url','directory','bankId','ledger','accounts'));
    }

    public function hints(Request $request)
    {
        $query = $request->input('query');
        $hints = Account::where('account_code', 'LIKE', "%{$query}%")
                     ->pluck('account_code'); 
        return response()->json($hints);
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

    public function fetchdata(Request $request,$date,$bank_id){
        //$rows = Model::whereDate('ledger_date', $date)->where('bank_id',$bank_id)->get();
        $rows = Model::whereHas('party', function ($query) {
                          $query->where('type', Party::POOL_TYPE_ZERO);
                      })
                      ->where('account_type', Model::ACCOUNT_TYPE_PARTY_VAL)
                      ->whereDate('ledger_date', $date)
                      ->where('bank_id',$bank_id)
                      ->get();

        $html = '';
        if ($rows->isEmpty()) {
            $html .= '';
        } else {
            foreach ($rows as $row) {
                $account_type = (($row->account_type==Model::ACCOUNT_TYPE_CLIENT_VAL && $row->original_account_type==Model::ACCOUNT_TYPE_CLIENT_VAL)?'client-row':(($row->account_type==Model::ACCOUNT_TYPE_CLIENT_VAL && $row->original_account_type==Model::ACCOUNT_TYPE_PARTY_VAL)?'pool-row':(($row->account_type==Model::ACCOUNT_TYPE_BANK_VAL)?'bank-row':'party-row')));
                $html .= '<tr class="'.$account_type.'">
                            <td class="excel-cell" contenteditable="true">'.$row->account_code.'</td>
                            <td class="excel-cell" contenteditable="true">'.Carbon::parse($row->ledger_date)->format('d/m/Y').'</td>
                            <td class="excel-cell" contenteditable="true">'.$row->utr_no.'</td>
                            <td class="excel-cell text-end" contenteditable="true">'.(($row->type == Model::LEDGER_TYPE_CREDIT_VAL) ? $row->amount : '').'</td>
                            <td class="excel-cell text-end" contenteditable="true">'.(($row->type == Model::LEDGER_TYPE_DEBIT_VAL) ? abs($row->amount) : '').'</td>
                            <td class="excel-cell text-end">'.$row->current_balance.'</td>
                            <td class="excel-cell" contenteditable="true">'.$row->remarks.'</td>
                            <td class="excel-cell"></td>
                            <td class="excel-cell"></td>
                            <td class="hide-cell">'.$row->transaction_id.'</td>
                          </tr>';
            }
        }
        //$carbonDate = Carbon::parse($date); 

        //$previousDate = $carbonDate->subDay(); 
        return json_encode(array('balance'=>closingBalance($date,$bank_id),'html'=>$html));
    }

    public function saveLedger(Request $request)
    {
        $data = $request->all();
        $account = Account::where('account_code', $data[0])->first();
        //dd($account);
        $account_type = (($account==null)?Model::ACCOUNT_TYPE_CLIENT_VAL:(($account->type==Account::CLIENT_ACCOUNT)?Model::ACCOUNT_TYPE_CLIENT_VAL:(($account->type==Account::BANK_ACCOUNT)?Model::ACCOUNT_TYPE_BANK_VAL:Model::ACCOUNT_TYPE_PARTY_VAL)));

        $carbonDate = Carbon::createFromFormat('d/m/Y', $data[1]);
        $dbFormatDate = $carbonDate->format('Y-m-d');
        $ledgerData = [
            'account_code' => $data[0],
            'account_type' => $account_type,
            'utr_no' => $data[2],
            'transaction_id' => $data[9],
            'employee_id' => Auth::user()->id,
            'ledger_date' => $dbFormatDate,
            'remarks' => $data[6],
            'bank_id' => $data[10]
        ];

        // Check if either credit or debit amount is provided and set accordingly
        if (!is_null($data[3])) {
            $ledgerData['amount'] = $data[3];
            $ledgerData['type'] = Model::LEDGER_TYPE_CREDIT_VAL;
        } elseif (!is_null($data[4])) {
            $ledgerData['amount'] = -abs($data[4]);
            $ledgerData['type'] = Model::LEDGER_TYPE_DEBIT_VAL;
        }

        //dd($ledgerData);
        try {
            if($ledgerData['amount']!=null){
                $entry = Model::where('bank_id', $data[10])->where('transaction_id',$data[9])->first();
                $ledgerData['remarks'] = "Pool entry from ".Carbon::parse($entry['ledger_date'])->format('d/m/Y h:i:s')." transfered to Account ".$data[0];
                Model::updateOrCreate(
                    ['bank_id' => $data[10], 'transaction_id' => $data[9]],
                    $ledgerData
                );
            }
            return response()->json(['success' => true, 'message' => 'Data saved successfully','background' => (($account_type==Model::ACCOUNT_TYPE_CLIENT_VAL)?'client-row':(($account_type==Model::ACCOUNT_TYPE_BANK_VAL)?'bank-row':'party-row')),'transaction_id'=>$data[8]]);
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

    public function list(Request $request)
    {
        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value'); // Getting search input
        $order = $request->input('order.0'); // Getting ordering input
        $columns = $request->input('columns');
        // $data = Model::where('account_type', Model::ACCOUNT_TYPE_PARTY_VAL)
        //               ->whereHas('party', function ($query) {
        //                   $query->where('type', Party::POOL_TYPE_ZERO);
        //               })
        //               ->withTrashed()
        //               ->latest();
        $query = Model::where('account_type', Model::ACCOUNT_TYPE_PARTY_VAL)
                      ->whereHas('party', function ($query) {
                          $query->where('type', Party::POOL_TYPE_ZERO);
                      })->when($search, function ($query, $search) {
                // Add your searchable columns here
                return $query->where(function ($q) use ($search) {
                    $q->where('account_code', 'like', "{$search}%")
                      ->orWhere('utr_no', 'like', "%{$search}%");
                });
            })->withTrashed()->latest();
        //$data = Model::withTrashed()->latest()->limit(500)->get();
        $filteredRecords = $query->count();

        // Paginate the results
        $query = $query->skip($start)->take($length);

        // Get the results
        $data = $query;
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
                $formattedDate = $date->format('d/m/y');
                return $formattedDate;
            })

            ->addColumn('entry_date', function ($row) {
                $date = Carbon::parse($row->created_at);
                $formattedDate = $date->format('d/m/y h:i:s A');
                return $formattedDate;
            })

            ->addColumn('created_by', function ($row) {
                $created_by = $row->user->name;
                return $created_by;
            })

            ->addColumn('transaction_id', function ($row) {
                $transaction_id = $row->transaction_id;
                return $transaction_id;
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
        ->with([
                'draw' => $request->input('draw'),
                'recordsTotal' => Model::count(),
                'recordsFiltered' => $filteredRecords,
            ])
        ->make(true);
    }


}
