<?php
use App\Models\ModuleMaster;
use App\Models\Bank;
use App\Models\CashbookLedger;
function softModules()
{
    return ModuleMaster::where('status',1)->get();
}


function dateFormatdMY($date){
    if($date=='' || $date==null){
        return '-';
    }
    return date('d M Y',strtotime($date));
}

function dateFormatdMYHia($date){
    if($date=='' || $date==null){
        return '-';
    }
    return date('d M Y H:iA',strtotime($date));
}
 
function permissions(){
    return $permissions = Auth::user()->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->toArray();
}

function bankAccount($account_code){
    return Bank::where('account_code',$account_code)->first();
}

function sumLedgerAmount($type,$date,$bank_id){
    return CashbookLedger::where('type',$type)->whereDate('ledger_date',$date)->where('bank_id',$bank_id)->where('account_type',CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)->sum('amount');
}

function closingBalance($date,$bank_id){
    $balance = CashbookLedger::where('bank_id', $bank_id)->whereDate('ledger_date', $date)->sum('amount');
    return $balance;
}