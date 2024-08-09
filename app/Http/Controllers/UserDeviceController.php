<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\UserDevice as Model;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class UserDeviceController extends Controller
{
    const TITLE = 'User Devices';
    const URL = 'user-devices';
    const DIRECTORY = 'userdevices';
    const FNAME = 'User Devices';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        //if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory'));
        // }else{
        //     return redirect()->route('dashboard.index');
        // }
    }

    public function list(Request $request)
    {
        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value'); 
        $order = $request->input('order')[0]; 
        $columns = $request->input('columns'); 
        $status = $request->input('status');

        $columnMap = [
            'client_code' => 'client_code',
            'name' => 'name',
            'username' => 'username',
            'client_address' => 'client_address',
            'address_type' => 'address_type',
        ];

        $orderColumnIndex = $order['column'];
        $orderDirection = $order['dir'];
        $orderColumnName = $columns[$orderColumnIndex]['data'];
        $orderByColumn = $columnMap[$orderColumnName] ?? 'id'; 

        $query = Model::with('client')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('client_address', 'like', "%{$search}%")
                      ->orWhereHas('client', function ($q) use ($search) {
                          $q->where('client_code', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                      });
                });
            })
            ->when($orderByColumn, function ($query) use ($orderByColumn, $orderDirection) {
                if (in_array($orderByColumn, ['client_code', 'username'])) {
                    return $query->whereHas('client', function ($q) use ($orderByColumn, $orderDirection) {
                        $q->orderBy($orderByColumn, $orderDirection);
                    });
                } else {
                    return $query->orderBy($orderByColumn, $orderDirection);
                }
            });

        // Use chunking for large data sets
        $filteredRecords = $query->count();

        $data = [];
        $query->skip($start)->take($length)->chunk(100, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $data[] = [
                    'account_id' => $row->client->client_code,
                    'name' => ucwords($row->client->name),
                    'username' => $row->client->username,
                    'address' => $row->client_address,
                    'type' => $row->address_type == 0 ? 'IP' : 'MAC',
                    'count' => $row->count,
                ];
            }
        });

        return DataTables::of(collect($data))
            ->rawColumns(['action'])
            ->with([
                'draw' => $request->input('draw'),
                'recordsTotal' => Model::count(),
                'recordsFiltered' => $filteredRecords,
            ])
            ->make(true);
    }

}
