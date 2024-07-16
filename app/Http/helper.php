<?php
use App\Models\ModuleMaster;
use App\Models\Bank;
use App\Models\CashbookLedger;
use App\Models\Role;
function softModules()
{
    return ModuleMaster::where('status',1)->get();
}

function buildRoleDropdown($roles, $parent_id = null, $prefix = ''){
    $output = [];

    foreach ($roles as $role) {
        if ($role->parent_id == $parent_id) {
            $output[$role->id] = $prefix . $role->name;
            $output += buildRoleDropdown($roles, $role->id, $prefix . '--');
        }
    }

    return $output;
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
    
    $permissions = Auth::user()->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->toArray();
    return $permissions;
}

function bankAccount($account_code){
    return Bank::where('account_code',$account_code)->first();
}

function sumLedgerAmount($type,$date,$bank_id){
    return CashbookLedger::where('type',$type)->whereDate('ledger_date',$date)->where('bank_id',$bank_id)->where('account_type',CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)->sum('amount');
}

function closingBalance($date,$bank_id){
    $balance = CashbookLedger::where('bank_id', $bank_id)->whereDate('ledger_date','<', $date)->sum('amount');
    return $balance;
}