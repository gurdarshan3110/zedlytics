<?php

namespace App\Http\Controllers;

use App\Models\LedgerLog;
use Illuminate\Http\Request;
use App\Models\LedgerLog as Model;
use DataTables;
use Carbon\Carbon;

class LedgerLogController extends Controller
{

    const TITLE = 'Ledger Logs';
    const URL = 'ledger-logs';
    const DIRECTORY = 'ledger_logs';
    const FNAME = 'Ledger Logs';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        //$logs = LedgerLog::with(['cashbookLedger', 'user'])->latest()->paginate(50);
        //return view('ledger_logs.index', compact('logs'));
        //if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory'));
        // }else{
        //     return redirect()->route('dashboard.index');
        // }
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $data = Model::with(['cashbookLedger', 'user'])->latest()->get();

        return DataTables::of($data)


            ->addColumn('id', function ($row) {
                $id = $row->cashbookLedger->transaction_id;

                return $id;
            })

            ->addColumn('user', function ($row) {
                $user = $row->user->name;

                return $user;
            })

            ->addColumn('action', function ($row) {
                $action = $row->action;

                return $action;
            })

            ->addColumn('description', function ($row) {
                $description = $row->description;

                if (is_array($description)) {
                    $descriptionString = '<ul>';
                    foreach ($description as $field => $change) {
                        $descriptionString .= "<li>{$field}: <b class='text-danger'>{$change['old']}</b> => <b class='text-success'>{$change['new']}</b></li>";
                    }
                    $descriptionString .= '</ul>';
                    return $descriptionString;
                }

                return $description;
            })

            ->addColumn('timestamp', function ($row) {
                $timestamp = Carbon::parse($row->created_at)->format('d/m/Y h:i a');

                return $timestamp;
            })
        ->rawColumns(['description'])
        ->make(true);
    }
}
