<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashbookLedgerExport;
use App\Models\Bank;

class ReportController extends Controller
{
    const TITLE = 'DW Report';
    const URL = 'dw report';
    const DIRECTORY = 'report';
    const FNAME = 'DW Report';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        $banks = Bank::where('status', 1)->pluck('account_code', 'id')
            ->prepend('Select Bank', '');
        if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory','banks'));
        }else{
            return redirect()->route('dashboard.index');
        }
    }
    public function generateExcelReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $bankId = $request->input('bank');

        $query = CashbookLedger::whereBetween('ledger_date', [$startDate, $endDate]);
                               

        if (!empty($bankId)) {
            $query->where('bank_id', $bankId);
        }

        $data = $query->whereNull('deleted_at')->get();


        return Excel::download(new CashbookLedgerExport($data,$bankId), 'cashbook_ledger_report.xlsx');
    }
}
