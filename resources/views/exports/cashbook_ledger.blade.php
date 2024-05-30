<table>
    <thead>
        <tr>
            <th>Account Code</th>
            <th>UTR No</th>
            <th>Deposit</th>
            <th>Withdraw</th>
            <th>Balance</th>
            <th>Ledger Date</th>
            <th>Entry Date</th>
            <th>Remarks</th>
            <th>Transaction ID</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row->account_code }}</td>
                <td>{{ $row->utr_no }}</td>
                <td>{{ $row->type == \App\Models\CashbookLedger::LEDGER_TYPE_CREDIT_VAL ? $row->amount : '' }}</td>
                <td>{{ $row->type == \App\Models\CashbookLedger::LEDGER_TYPE_DEBIT_VAL ? abs($row->amount) : '' }}</td>
                <td>{{ (($bank_id=='')?$row->balance:$row->bank_balance) }}</td>
                <td>{{ \Carbon\Carbon::parse($row->ledger_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i A') }}</td>
                <td>{{ $row->remarks }}</td>
                <td>{{ $row->transaction_id }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
