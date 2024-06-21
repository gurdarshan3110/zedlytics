<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Employee as Model;
use App\Models\UserEmployee;
use App\Models\MacAddress;
use App\Models\ModelHasRole;
use Spatie\Permission\Models\Role;
use App\Models\User;
use DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Employees';
    const URL = 'employees';
    const DIRECTORY = 'employee';
    const FNAME = 'Employee';

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
        $roles = Role::pluck('name', 'name')->prepend('Select Role', '');
        return view(self::DIRECTORY.'.create', compact('title','url','directory','roles'));
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
            'email' => 'required|email|unique:users',
            'password' => 'required',
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
        $input['employee_code'] = Model::generateEmployeeCode($input['name']);
        $input['user_type'] = User::USER_EMPLOYEE;
        $employee = Model::create($input);
        $user = User::create($input);
        $user->employees()->attach($employee);
        $user->assignRole($input['role']);

        return redirect()->route(self::URL.'.index', $employee->id)->with('success', self::FNAME.' created successfully.');
    }

    public function resetpassword(Request $request, Model $employee)
    {
        $input = $request->all();
        $rules = [
            'password' => 'required',
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

        $user = User::where('employee_code',$employee->employee_code)->first();
        //dd($employee->employee_code);
        $password = Hash::make($input['password']);
        $user->password = $password;
        $user->save();

        return redirect()->route(self::URL.'.index', $employee->id)->with('success', self::FNAME.' Password reset successfully.');
    }

    public function macaddress(Request $request, Model $employee)
    {
        $input = $request->all();
        $rules = [
            'mac_address' => 'required',
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

        $user = User::where('employee_code',$employee->employee_code)->first();
        $macAddress = explode(',',$input['mac_address']);
        $deleteMac = MacAddress::where('user_id',$user->id)->delete();
        foreach ($macAddress as $key => $value) {
            MacAddress::create([
                    'user_id' => $user->id,
                    'mac_address' => $value
                ]);
        }
        // $macArray = [
        //     'mac_address' => $input['mac_address']
        // ];

        // MacAddress::updateOrCreate(
        //             ['user_id' => $user->id],
        //             $macArray
        //         );
        
        return redirect()->route(self::URL.'.index', $employee->id)->with('success', self::FNAME.' Mac Address updated successfully.');
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
        $employee = UserEmployee::find($id);

        //return view(self::DIRECTORY.'.show', compact(self::DIRECTORY, 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Model $employee)
    {
        $title = 'Edit '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $roles = Role::pluck('name', 'name')->prepend('Select Role', '');
        return view(self::DIRECTORY.'.edit', compact(self::DIRECTORY, 'title','directory','url','roles'));
    }

    public function reset(Model $employee)
    {
        $title = 'Reset '.self::FNAME .' Password';
        $url = self::URL;
        $directory = self::DIRECTORY;
        $roles = Role::pluck('name', 'name')->prepend('Select Role', '');
        return view(self::DIRECTORY.'.reset', compact(self::DIRECTORY, 'title','directory','url','roles'));
    }

    public function mac(Model $employee)
    {
        $title = 'Add '.$employee->name .' Mac Address';
        $url = self::URL;
        $directory = self::DIRECTORY;
        $roles = Role::pluck('name', 'name')->prepend('Select Role', '');
        $user = User::where('employee_code',$employee->employee_code)->first();
        $macAddress = MacAddress::where('user_id',$user->id)->get();
        $mac_address = '';
        foreach ($macAddress as $key => $value) {
            $mac_address = (($key==0)?$value->mac_address:$mac_address.','.$value->mac_address);
        }
        
        return view(self::DIRECTORY.'.mac', compact(self::DIRECTORY, 'title','directory','url','roles','mac_address'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Model $employee)
    {
        $input = $request->all();
        $user = User::where('email', $employee->email)->first();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,id,'.$user->id,
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
        ModelHasRole::where('model_id',$user->id)->delete();
        //$user->removeRole($user->role);
        $employee->update($input);
        $user->update(['name' => $request->name,'email' => $request->email,'role' => $request->role]);
        $user->assignRole($input['role']);

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $employee)
    {
        $employee->delete();

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

            ->addColumn('account_code', function ($row) {
                $account_code = $row->employee_code;

                return $account_code;
            })

            ->addColumn('email', function ($row) {
                $email = $row->email;

                return $email;
            })

            ->addColumn('phone_no', function ($row) {
                $phone_no = $row->phone_no;

                return $phone_no;
            })

            ->addColumn('role', function ($row) {
                $role = $row->role;

                return $role;
            })
            ->addColumn('action', function ($row) {
                $msg = 'Are you sure?';
                $action = '<form action="'.route(self::URL.'.destroy', [$row]).'" method="post">
                    '.csrf_field().'
                    '.method_field('DELETE').'
                    <div class="btn-group">
                    '.((in_array('edit '.self::DIRECTORY, permissions()))?'
                    <a href="'.route(self::URL.'.edit', [$row]).'"
                       class="btn btn-info btn-xs">
                        <i class="far fa-edit"></i>
                    </a><a href="'.route(self::URL.'.mac', [$row]).'"
                       class="btn btn-info btn-xs">
                        <i class="fa fa-laptop"></i>
                    </a><a href="'.route(self::URL.'.reset', [$row]).'"
                       class="btn btn-warning btn-xs">
                        <i class="fa fa-key"></i>
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
