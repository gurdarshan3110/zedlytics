<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashbookLedgerExport implements FromView
{
    protected $data,$bankId;

    public function __construct($data,$bankId)
    {
        $this->data = $data;
        $this->bank_id = $bankId;
    }

    public function view(): View
    {
        return view('exports.cashbook_ledger', [
            'rows' => $this->data,
            'bank_id' => $this->bank_id,
        ]);
    }
}
