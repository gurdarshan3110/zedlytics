<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as Model;
use Spatie\Permission\Models\Permission;
use DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Roles';
    const URL = 'roles';
    const DIRECTORY = 'role';
    const FNAME = 'Role';

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
        $permissions = Permission::all();
        $roles = Model::all();
        $roleOptions = buildRoleDropdown($roles);
        //dd($roleOptions);
        return view(self::DIRECTORY.'.create', compact('title','url','directory','permissions','roleOptions'));
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
        //dd($input);
        $rules = [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ];

        $validator = Validator::make($input, $rules);
        
        if(in_array('employee dashboard',permissions())){
            $permission = Permission::select('id')->where('name', 'dashboard')->first();
            $input['permissions'] = array_diff($input['permissions'], [$permission->id]);
        }
        if ($validator->fails()) {
            $errors = '';
            foreach ($validator->errors()->all() as $error) {
                $errors = $errors.$error;
            }
            return redirect()->route(self::URL.'.index')
                    ->with('error',$errors)
                    ->withInput();
        }

        // Create the role
        $role = Model::create([
            'name' => $input['name'],
            'parent_id' => $input['parent_id'],
            'guard_name' => 'web',
        ]);

        $permissions = Permission::whereIn('id', $input['permissions'])->get();
        $role->givePermissionTo($permissions);

        return redirect()->route(self::URL.'.index', $role->id)->with('success', self::FNAME.' created successfully.');
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
    public function edit(Model $role)
    {
        $title = 'Edit '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $permissions = Permission::all();
        $roles = Model::all();
        $roleOptions = buildRoleDropdown($roles);

        return view(self::DIRECTORY.'.edit', compact(self::DIRECTORY, 'title','directory','url','permissions','roleOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Model $role)
    {
        $input = $request->all();
        
        if(in_array('employee dashboard',permissions())){
            $permission = Permission::select('id')->where('name', 'dashboard')->first();
            $input['permissions'] = array_diff($input['permissions'], [$permission->id]);
        }
        $rules = [
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
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
        // Update the role name
        $role->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
        ]);

        $permissions = Permission::whereIn('id', $input['permissions'])->get();
        $role->syncPermissions($permissions);

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $role)
    {
        $role->delete();

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

    public function getPermissionsByParent($parentId)
    {
        $role = Model::find($parentId);

        if (!$role) {
            return response()->json(['error' => 'Role not found.'], 404);
        }

        $permissions = Permission::all();
        $roles = Model::all();

        // Generate the HTML content
        $content = '<div class="form-check mb-3">
                        <input type="checkbox" id="select-all-permissions" class="form-check-input">
                        <label for="select-all-permissions" class="form-check-label">Select All</label>
                    </div>';

        $permissionsByParent = $permissions->groupBy('parent');
        $lastParentName = null;

        foreach ($permissionsByParent as $parentId => $permissionsGroup) {
            $parentName = $permissionsGroup->first()->parent;

            if ($parentName !== $lastParentName) {
                $content .= '<div class="permission-group mb-4">
                                <h5>' . ucwords($parentName) . '</h5>
                                <div class="row">';
            } else {
                $content .= '<div class="row mt-3">';
            }

            foreach ($permissionsGroup as $permission) {
                $checked = $role->permissions->contains('id', $permission->id) ? 'checked' : '';
                $content .= '<div class="col-sm-3">
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="permissions[]" 
                                        value="' . $permission->id . '" 
                                        id="permission_' . $permission->id . '" 
                                        class="form-check-input permission-checkbox"
                                        ' . $checked . '
                                    >
                                    <label 
                                        for="permission_' . $permission->id . '" 
                                        class="form-check-label">
                                        ' . ucwords($permission->name) . '
                                    </label>
                                </div>
                            </div>';
            }

            $content .= '</div></div>';

            $lastParentName = $parentName;
        }

        return $content;
    }



}
