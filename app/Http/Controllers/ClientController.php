<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Client as Model;
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

    const TITLE = 'Clients';
    const URL = 'clients';
    const DIRECTORY = 'client';
    const FNAME = 'Client';

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
        return view(self::DIRECTORY.'.create', compact('title','url','directory'));
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
            'email' => 'required|email|unique:clients',
            'account_code' => 'required|unique:accounts',
            'phone_no' => ['required', 'regex:/^(\+\d{1,3}[- ]?)?\d{10,}$/', 'unique:'.self::URL],
            'status' => 'required'
        ]);

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
        $employee = UserClient::find($id);

        //return view(self::DIRECTORY.'.show', compact(self::DIRECTORY, 'title'));
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
        return view(self::DIRECTORY.'.edit', compact(self::DIRECTORY, 'title','directory','url'));
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
        $account = Account::where('account_code', $client->account_code)->first();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'account_code' => 'required|unique:accounts,id,'.$account->id,
            'email' => 'required|email|unique:users,id,'.$user->id,
            'phone_no' => [
                'required',
                'regex:/^(\+\d{1,3}[- ]?)?\d{10,}$/',
                'unique:'.self::URL.',phone_no,'.$client->id.',id'
            ],
            'status' => 'required',
        ]);
        $client->update($input);
        $account->update(['name' => $request->name]);

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
        $input = $request->all();
        $data = Model::where('status',$input['status'])->latest()->get();

        return DataTables::of($data)


            ->addColumn('name', function ($row) {
                $name = $row->name;

                return $name;
            })

            ->addColumn('email', function ($row) {
                $email = $row->email;

                return $email;
            })

            ->addColumn('phone_no', function ($row) {
                $phone_no = $row->phone_no;

                return $phone_no;
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
