<?php
use App\Models\ModuleMaster;
use App\Models\Bank;
use App\Models\CashbookLedger;
use App\Models\Role;
use App\Models\Permission;

function softModules($parent)
{
    return ModuleMaster::where('status',1)->where('parent',$parent)->orderBy('sno')->get();
}

function renderModule($module, $permissions) {
    $moduleurl = str_replace('-', ' ', $module->url);
    if ($module->url == 'margin-limit-menu') {
        $moduleurl = 'margin limit';
    }
    $permissionKey = ($module->parent == 0 ? 'dashboard' : 'view ' . $moduleurl);
    if (in_array($permissionKey, $permissions)) {
        echo '<a class="nav-link '. (request()->is($module->url . '*') ? 'active' : '') .'" href="'. route($module->url . '.index') .'">
            <div class="sb-nav-link-icon"><i class="fas '. $module->icon .'"></i></div>
            '. $module->name .'
        </a>';
    }
}

function renderMenu($parent, $modules, $permissions) {
    $children = $modules->filter(function($module) use ($parent) {
        return $module->parent == $parent;
    });
    if ($children->isNotEmpty()) {
        echo '<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapse'. $parent .'" aria-expanded="false" aria-controls="collapse'. $parent .'">
            <div class="sb-nav-link-icon"><i class="fas fa-bars"></i></div>
            '. ($parent == 0 ? 'Menu List' : $parent->name) .'
            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="collapse'. $parent .'" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
            <nav class="sb-sidenav-menu-nested nav">';
        foreach ($children as $child) {
            renderModule($child, $permissions);
        }
        echo '</nav></div>';
    }
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