<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\EquityRecord as Model;
use App\Models\Brand;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class AggregateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Aggregates';
    const URL = 'aggregate';
    const DIRECTORY = 'aggregate';
    const FNAME = 'Aggregate';

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
        $brands = Brand::where('status', 1)->pluck('name', 'id')
            ->prepend('Select Brand', '');
        return view(self::DIRECTORY.'.create', compact('title','url','directory','brands'));
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
            'deposit' => 'required',
            'withdraw' => 'required',
            'equity' => 'required',
            'brand_id' => 'required',
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
        $input['ledger_date'] = Carbon::today()->toDateString();
        $input['user_id'] = Auth::user()->id;
        $equity = Model::create($input);

        return redirect()->route(self::URL.'.index', $equity->id)->with('success', self::FNAME.' created successfully.');
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
    public function edit(Model $aggregate)
    {
        $title = 'Edit '.self::FNAME;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $brands = Brand::where('status', 1)->pluck('name', 'id')
            ->prepend('Select Brand', '');
        $equityrecord = $aggregate;
        return view(self::DIRECTORY.'.edit', compact(self::DIRECTORY, 'title','directory','url','brands','aggregate','equityrecord'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Model $aggregate)
    {
        $input = $request->all();
        $rules = [
            'deposit' => 'required',
            'withdraw' => 'required',
            'equity' => 'required',
            'brand_id' => 'required',
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
        
        $aggregate->update($input);

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $equityrecord)
    {
        $equityrecord->delete();

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' deleted successfully.');
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $data = Model::where('status',$input['status'])->latest()->get();

        return DataTables::of($data)


            ->addColumn('brand', function ($row) {
                $brand = $row->brand->name;

                return $brand;
            })

            ->addColumn('date', function ($row) {
                $date = Carbon::parse($row->ledger_date)->format('d/m/Y');

                return $date;
            })

            ->addColumn('equity', function ($row) {
                $equity = $row->equity;

                return $equity;
            })

            ->addColumn('deposit', function ($row) {
                $deposit = $row->deposit;

                return $deposit;
            })

            ->addColumn('withdraw', function ($row) {
                $withdraw = $row->withdraw;

                return $withdraw;
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
