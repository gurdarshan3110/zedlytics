<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Bank as Model;
use App\Models\Account;
use App\Models\Permission;
use App\Models\User;
use DataTables;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Banks';
    const URL = 'banks';
    const DIRECTORY = 'bank';
    const FNAME = 'Bank';

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
            'account_code' => 'required|unique:accounts',
            'status' => 'required'
        ]);

        $input['type'] = Account::BANK_ACCOUNT;
        $bank = Model::create($input);
        $account = Account::create($input);
        $account->banks()->attach($bank);
        Permission::create([
            'name' => $input['account_code'],
            'guard_name' => 'web',
            'parent' => 'New Bank Account'
        ]);
        return redirect()->route(self::URL.'.index', $bank->id)->with('success', self::FNAME.' created successfully.');
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
    public function edit(Model $bank)
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

    public function update(Request $request, Model $bank)
    {
        $input = $request->all();
        $account = Account::where('account_code', $bank->account_code)->first();
        $validatedData = $request->validate([
            'name' => 'required',
            'account_code' => 'required|unique:accounts',
            'status' => 'required'
        ]);

        $permission = Permission::where('name', $bank->account_code)->first();
        $permission->name = $input['account_code'];
        $permission->update();
        $bank->update($input);
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
    public function destroy(Model $bank)
    {
        $bank->delete();
        
        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' deleted successfully.');
    }

    public function list()
    {
        $data = Model::latest()->get();

        return DataTables::of($data)


            ->addColumn('name', function ($row) {
                $name = $row->name;

                return $name;
            })

            ->addColumn('account_no', function ($row) {
                $account_no = $row->account_no;

                return $account_no;
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
