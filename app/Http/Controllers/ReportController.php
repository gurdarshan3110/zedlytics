<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashbookLedger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashbookLedgerExport;
use App\Models\Brand;
use App\Models\Bank;
use DataTables;
use Carbon\Carbon;

class ReportController extends Controller
{
    const TITLE = 'DW Report';
    const TITLE1 = 'Financials Report';
    const URL = 'report';
    const URL1 = 'financial-report';
    const DIRECTORY = 'report';
    const DIRECTORY1 = 'financialreport';
    const FNAME = 'DW Report';
    const FNAME1 = 'Financial Report';

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

    public function findex(Request $request)
    {
        $title = self::TITLE1;
        $url = self::URL1;
        $directory = self::DIRECTORY1;
        $furl = str_replace('-', ' ', $url);

        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Use current date if start_date and end_date are not provided
        $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : Carbon::now()->startOfDay();
        $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::now()->endOfDay();
        //dd($startDate.' '.$endDate);
        $brands = Brand::all();
        $reportData = [];

        $currentDate = clone $startDate;
        while ($currentDate->lte($endDate)) {
            foreach ($brands as $brand) {
                $start = Carbon::parse($currentDate)->startOfDay();
                $end = Carbon::parse($currentDate)->endOfDay();
                $deposits = $brand->depositsBetween($start, $end);
                $withdrawals = $brand->withdrawalsBetween($start, $end);
                $parking = $brand->parkingsupto($start, $end);

                $gap = $deposits['deposit'] - $withdrawals['withdraw'];
                $pool = $brand->poolBetween($start, $end);
                $reportData[] = [
                    'date' => $currentDate->toDateString(),
                    'brand' => $brand->name,
                    'deposits' => $deposits['deposit'],
                    'withdraw' => $withdrawals['withdraw'],
                    'gap' => $gap,
                    'pool' => $pool['pool'],
                    'parking' => $parking,
                ];
            }
            $currentDate->addDay();
        }

        if (in_array('view ' . $furl, permissions())) {
            return view($directory . '.index', compact('title', 'url', 'directory', 'reportData'));
        } else {
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
        $bank = Bank::find($bankId);

        return Excel::download(new CashbookLedgerExport($data,$bankId), $bank->name."-".$startDate.'cashbook_ledger_report.xlsx');
    }
}
