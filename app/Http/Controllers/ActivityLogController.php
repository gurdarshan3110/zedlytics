<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DataTables;

class ActivityLogController extends Controller
{
    const TITLE = 'Timeline';
    const URL = 'timeline';
    const DIRECTORY = 'timeline';
    const FNAME = 'Timeline';
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
        $rules = [
            'name' => 'required',
            'account_code' => 'required|unique:accounts',
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

        $input['type'] = Account::BRAND_ACCOUNT;
        $brand = Model::create($input);
        $account = Account::create($input);
        $account->banks()->attach($brand);

        return redirect()->route(self::URL.'.index', $brand->id)->with('success', self::FNAME.' created successfully.');
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
    public function edit(Model $brand)
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

    public function update(Request $request, Model $brand)
    {
        $input = $request->all();
        $account = Account::where('account_code', $brand->account_code)->first();
        $rules = [
            'name' => 'required',
            'account_code' => 'required|unique:accounts,id,'.$account->id,
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
        
        $brand->update($input);
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
    public function destroy(Model $brand)
    {
        $brand->delete();

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' deleted successfully.');
    }

    public function list(Request $request)
    {
        $input = $request->all();

        // Fetch user-wise daily time in hours
        $activityLogs = ActivityLog::select(
                'user_id',
                DB::raw('DATE(start_time) as date')
            )
            ->groupBy('user_id', 'date')
            ->with('user')
            ->get();

        // Add total hours for each user to the collection
         $activityLogs->map(function ($activityLog) {
            $activityLog->total_hours = number_format(ActivityLog::calculateTotalHours($activityLog->user_id, $activityLog->date, $activityLog->date), 2);
            return $activityLog;
        });

        return DataTables::of($activityLogs)
            ->addColumn('user', function ($row) {
                return $row->user->name;
            })
            ->addColumn('date', function ($row) {
                return $row->date;
            })
            ->addColumn('total_hours', function ($row) {
                return $row->total_hours;
            })
            ->make(true);
    }
}

