<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Client as Model;
use App\Models\Brand;
use App\Models\ClientLog;
use App\Models\Account;
use App\Models\User;
use DataTables;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Users';
    const URL = 'clients';
    const DIRECTORY = 'client';
    const FNAME = 'User';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory'));
        }else{
            return redirect()->route('dashboard.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add New '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $role = 'relationship manager';
        $brands = Brand::where('status',1)->pluck('name','id')
            ->prepend('Select Brand', '');
        $rms = User::where('status', 0)
           ->whereRaw('LOWER(role) = ?', [strtolower($role)])
           ->pluck('name', 'id')
           ->prepend('Select Relationship Manager', '');
        return view(self::DIRECTORY.'.create', compact('title','url','directory','rms','brands'));
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
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:clients',
            'account_code' => 'required|unique:accounts',
            'phone_no' => 'required',
            'status' => 'required'
        ];

        $validator = Validator::make($input, $rules);
        
        if ($validator->fails()) {
            $errors = '';
            foreach ($validator->errors()->all() as $error) {
                $errors = $errors.$error;
            }
            return redirect()->route(self::URL.'.index')
                    ->with('error',$errors)
                    ->withInput();
        }

        $input['client_code'] = $input['account_code'];
        $input['type'] = Account::CLIENT_ACCOUNT;
        $client = Model::create($input);
        $account = Account::create($input);
        $account->clients()->attach($client);

        return redirect()->route(self::URL.'.index', $client->id)->with('success', self::FNAME.' created successfully.');
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
        $client = Model::find($id);
        $url = self::URL;
        $directory = self::DIRECTORY;
        return view(self::DIRECTORY.'.show', compact(self::DIRECTORY, 'title','directory','url','client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Model $client)
    {
        $title = 'Edit '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $role = 'relationship manager';
        $brands = Brand::where('status',1)->pluck('name','id')
            ->prepend('Select Brand', '');
        $rms = User::where('status', 0)
           ->whereRaw('LOWER(role) = ?', [strtolower($role)])
           ->pluck('name', 'id')
           ->prepend('Select Relationship Manager', '');
        return view(self::DIRECTORY.'.edit', compact(self::DIRECTORY, 'title','directory','url','rms','brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Model $client)
    {
        $input = $request->all();
        $account = Account::where('account_code', $client->client_code)->where('type',Account::CLIENT_ACCOUNT)->first();
        $rules = [
            'name' => 'required|string|max:255',
            'account_code' => 'required|unique:accounts,account_code,'.$account->id.',id', // Corrected to use the correct column name
            'phone_no' => 'required',
            'status' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        
        if ($validator->fails()) {
            $errors = '';
            foreach ($validator->errors()->all() as $error) {
                $errors = $errors.$error;
            }
            return redirect()->route(self::URL.'.index')
                    ->with('error',$errors)
                    ->withInput();
        }

        $client->update($input);
        $account->update($input);

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $client)
    {
        $client->delete();

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' deleted successfully.');
    }

    public function list(Request $request)
    {
        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value'); // Getting search input
        $order = $request->input('order.1'); // Getting ordering input
        $columns = $request->input('columns'); // Getting column data
        $status = $request->input('status');
        
        // Fetch the query with filtering and ordering
        $query = Model::where('status', $status)
            ->when($search, function ($query, $search) {
                // Add your searchable columns here
                return $query->where(function ($q) use ($search) {
                    $q->where('client_code', 'like', "{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "{$search}%")
                      ->orWhere('phone_no', 'like', "%{$search}%");
                });
            })
            ->orderBy($columns[$order['column']]['data'], $order['dir']);

        // Get the total number of records after filtering
        $filteredRecords = $query->count();

        // Paginate the results
        $query = $query->skip($start)->take($length);

        // Get the results
        $data = $query;

        // Prepare DataTables response
        return DataTables::of($data)
            ->addColumn('brand', function ($row) {
                return (($row->brand_id!=null)?$row->brand->name:'');
            })
            ->addColumn('client_code', function ($row) {
                return $row->client_code;
            })
            ->addColumn('username', function ($row) {
                return $row->username;
            })
            ->addColumn('name', function ($row) {
                return ucwords($row->name);
            })
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('phone_no', function ($row) {
                return $row->phone_no;
            })
            ->addColumn('rm', function ($row) {
                return (($row->rm!=null)?$row->rmanager->name:'');
            })
            ->addColumn('action', function ($row) {
                $msg = 'Are you sure?';
                $action = '<form action="'.route(self::URL.'.destroy', [$row]).'" method="post">
                    '.csrf_field().'
                    '.method_field('DELETE').'
                    <div class="btn-group">
                    <a href="'.route(self::URL.'.show', [$row]).'"
                           class="btn btn-success btn-xs">
                            <i class="far fa-eye"></i>
                        </a>
                    '.(in_array('edit '.self::DIRECTORY, permissions()) ? '
                    <a href="'.route(self::URL.'.edit', [$row]).'"
                       class="btn btn-warning btn-xs">
                        <i class="far fa-edit"></i>
                    </a>' : '').(in_array('delete '.self::DIRECTORY, permissions()) ? '
                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\''.$msg.'\')"><i class="far fa-trash-alt"></i></button>' : '').'
                </div>
                </form>';

                return $action;
            })
            ->rawColumns(['action'])
            ->with([
                'draw' => $request->input('draw'),
                'recordsTotal' => Model::where('status', $status)->count(),
                'recordsFiltered' => $filteredRecords,
            ])
            ->make(true);
    }

    public function addNotes(Request $request, Model $client)
    {
        //dd($request);
        $request->validate([
            'note' => 'required|string',
        ]);

        ClientLog::create([
            'client_id' => $request->client_id,
            'user_id' => Auth::id(),
            'note' => $request->note,
            'log_type' => 'note',
        ]);

        return redirect()->back()->with('success', 'Note added successfully.');
    }

    function generateRandomString($name) {
        // Convert name to uppercase
        $name = strtoupper($name);
        
        // Get the first three letters from the name
        $letters = substr($name, 0, 3);
        
        // Generate three random numbers
        $numbers = mt_rand(100, 999);
        
        // Combine letters and numbers
        $randomString = $letters . $numbers;
        
        return $randomString;
    }

}
