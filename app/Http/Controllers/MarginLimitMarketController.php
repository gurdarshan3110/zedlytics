<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\MarginLimitMarket as Model;
use App\Models\User;
use DataTables;

class MarginLimitMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const TITLE = 'Margin And Limits Menu';
    const URL = 'margin-limit-menu';
    const DIRECTORY = 'marginlimit';
    const FNAME = 'Margin And Limits Menu';

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
            'market' => 'required',
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

        $input['status'] = 0;
        $market = Model::create($input);

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
    public function edit(Model $marginlimitmarket)
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

    public function update(Request $request, Model $marginlimitmarket)
    {
        $input = $request->all();
        $rules = [
            'market' => 'required|string|max:255',
            
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

        $marginlimitmarket->update($input);
        
        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $marginlimitmarket)
    {
        $marginlimitmarket->delete();

        return redirect()->route(self::URL.'.index')
                         ->with('success', self::FNAME.' deleted successfully.');
    }

    

    public function list(Request $request)
    {
        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value'); // Getting search input
        $order = $request->input('order.0'); // Getting ordering input
        $columns = $request->input('columns'); // Getting column data
        $status = $request->input('status');
        
        // Fetch the query with filtering and ordering
        $query = Model::where('status', $status)
            ->when($search, function ($query, $search) {
                // Add your searchable columns here
                return $query->where(function ($q) use ($search) {
                    $q->where('market', 'like', "{$search}%")
                      ->orWhere('script', 'like', "%{$search}%")
                      ->orWhere('minimum_deal', 'like', "{$search}%")
                      ->orWhere('maximum_deal_in_single_order', 'like', "%{$search}%")
                      ->orWhere('maximum_quantity_in_script', 'like', "%{$search}%");
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
            ->addColumn('id', function ($row) {
                return $row->id;
            })
            ->addColumn('market', function ($row) {
                return $row->market;
            })
            ->addColumn('script', function ($row) {
                return $row->script;
            })
            ->addColumn('minimum_deal', function ($row) {
                return $row->minimum_deal;
            })
            ->addColumn('maximum_deal_in_single_order', function ($row) {
                return $row->maximum_deal_in_single_order;
            })
            ->addColumn('maximum_quantity_in_script', function ($row) {
                return $row->maximum_quantity_in_script;
            })
            ->addColumn('intraday_margin', function ($row) {
                return $row->intraday_margin;
            })
            ->addColumn('holding_maintainence_margin', function ($row) {
                return $row->holding_maintainence_margin;
            })
            ->addColumn('inventory_day_margin', function ($row) {
                return $row->inventory_day_margin;
            })
            ->addColumn('total_group_limit', function ($row) {
                return $row->total_group_limit;
            })
            ->addColumn('margin_calculation_time', function ($row) {
                return $row->margin_calculation_time;
            })
            ->addColumn('total_group_limit', function ($row) {
                return $row->total_group_limit;
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


}
