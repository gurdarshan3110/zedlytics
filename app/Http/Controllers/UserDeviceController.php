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
        $orderByColumn = $columnMap[$orderColumnName] ?? 'client_code'; 

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
        $query->skip($start)->take($length)->chunk(50, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $rowData = [
                    'account_id' => $row->client->client_code,
                    'name' => ucwords($row->client->name),
                    'username' => $row->client->username,
                    'address' => $row->client_address,
                    'type' => $row->address_type == 0 ? 'IP' : 'MAC',
                    'count' => $row->count,
                ];

                // Add the link if count is greater than 1
                if ($row->count > 1) {
                    $link = route('device.details', ['id' => $row->id]);
                    $rowData['address'] = '<a href="' . $link . '" class="text-decoration-none">' . $row->client_address . '</a>';
                    $rowData['count'] = '<a href="' . $link . '" class="text-decoration-none">' . $row->count . '</a>';
                }

                $data[] = $rowData; // Add the row data to the data array
            }
        });

        return DataTables::of(collect($data))
            ->rawColumns(['address', 'count']) // Specify that address and count should be treated as HTML
            ->with([
                'draw' => $request->input('draw'),
                'recordsTotal' => Model::count(),
                'recordsFiltered' => $filteredRecords,
            ])
            ->make(true);

    }

    public function deviceDetail(Request $request,$id){
        $title = "Device Details";
        $url = self::URL;
        $directory = self::DIRECTORY;
        $fname = self::FNAME;
        $address = Model::find($id);
        $allUsers = Model::with('client')->where('client_address',$address->client_address)->get();
        return view($directory.'.device-details', compact( 'title','allUsers','url','directory'));
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $rules = [
            'id' => 'required|integer',
            'reason' => 'required',
        ];

        $device=Model::find($input['id']);
        $device->reason = $input['reason'].' | Updated By '.Auth::user()->name.' On '.date('d M,Y H:iA');
        $device->updated_by = Auth::user()->id;
        $device->is_available = $device->is_available==0?1:0;
        $device->save();
        return true;
    }


}
