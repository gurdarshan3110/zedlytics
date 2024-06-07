@extends('includes.app')

@section('content')
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}} 
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table-bordered table">
                <thead>
                    <th class="w-25 text-center">Bank</th>
                    <th class="w-25 text-end">Deposit</th>
                    <th class="w-25 text-end">Withdraw</th>
                    <th class="w-25 text-end">Gap</th>
                </thead>
                <tbody>
                    <?php
                        $totDeposit = 0;
                        $totWithdraw = 0;
                    ?>
                    @foreach($banks as $bank)
                        <tr>
                            <?php
                                $deposit = sumLedgerAmount(\App\Models\CashbookLedger::LEDGER_TYPE_CREDIT_VAL,$date,$bank->id);
                                $withdraw = sumLedgerAmount(\App\Models\CashbookLedger::LEDGER_TYPE_DEBIT_VAL,$date,$bank->id);
                                $gap = $deposit + $withdraw;
                                $totDeposit = $totDeposit + $deposit;
                                $totWithdraw = $totWithdraw + $withdraw;
                            ?>
                            <td>{{$bank->name}}</td>
                            <td class="text-end">{{number_format($deposit,2, '.', '')}}</td>
                            <td class="text-end">{{number_format(abs($withdraw),2, '.', '')}}</td>
                            <td class="text-end">{{number_format($gap,2, '.', '')}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="w-25 text-center">Total</th>
                        <th class="w-25 text-end">{{number_format($totDeposit, 2, '.', '')}}</th>
                        <th class="w-25 text-end">{{number_format(abs($totWithdraw), 2, '.', '')}}</th>
                        <th class="w-25 text-end">{{ $totDeposit + $totWithdraw}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

@endsection
