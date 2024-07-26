<?php
use App\Models\ModuleMaster;
use App\Models\Bank;
use App\Models\CashbookLedger;
use App\Models\Role;
use App\Models\Permission;
use Carbon\Carbon;

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

function nameInitials($name){
    preg_match_all('/\b\w/', $name, $matches);
    $initials = implode('', $matches[0]);
    return $initials;
}

function financialCard($id,$date,$deposit,$withdraw,$gap,$parking,$equity,$actualDeposit,$actualWithdraw,$depositCount,$withdrawCount,$lastdeposit,$lastwithdraw,$lastgap,$lastparking,$lastequity){
    $content = '
        <div class="row">
            <!-- First half of the card -->
            <div class="col-md-12 d-flex flex-column justify-content-center">
                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/'.$date->toDateString().'/'.$id.'">
                    <div class="card-text d-flex text-dark border-bottom-1">
                        <div class="w-75 fs-3">
                            '.(($actualDeposit!='' && $deposit==$actualDeposit)?'<i class="fa fa-check text-success me-4px" aria-hidden="true"></i>':'<i class="fa fa-times text-danger me-4px" aria-hidden="true"></i>').format_amount($deposit).'
                        </div>
                        <div class="w-25 d-flex align-items-center">
                            '.(($deposit>=$lastdeposit)?'<i class="fa fa-angle-double-up text-success  me-4px fs-5 me-0" aria-hidden="true"></i>':'<i class="fa fa-angle-double-down text-danger  me-4px fs-5 me-0" aria-hidden="true"></i>').'
                            <span class="fs-7 ms-1 mt-2">
                                '.$depositCount.'
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mt-2">
                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/'.$date->toDateString().'/'.$id.'">
                    <p class="text-center m-0">Withdrawal</p>
                    <div class="card-text d-flex text-dark m-0 text-center">
                        <div class="w-85 fs-14 text-center">
                            '.(($actualWithdraw!='' && $withdraw==$actualWithdraw)?'<i class="fa fa-check text-success me-4px" aria-hidden="true"></i>':'<i class="fa fa-times text-danger me-4px" aria-hidden="true"></i>').format_amount($withdraw).'
                        </div>
                        <div class="w-15 d-flex align-items-center text-end">
                            '.(($withdraw>=$lastwithdraw)?'<i class="fa fa-long-arrow-up text-success  me-2px fs-14 me-0" aria-hidden="true"></i>':'<i class="fa fa-long-arrow-down text-danger  me-4px fs-14 me-0" aria-hidden="true"></i>').' 
                            <span class="fs-7 ms-1 mt-2">
                                '.$withdrawCount.'
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mt-2">
                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/'.$date->toDateString().'/'.$id.'">
                    <p class="text-center m-0">Equity</p>
                    <div class="card-text d-flex text-dark m-0 text-center">
                        <div class="w-85 fs-14 text-center">
                            '.format_amount($equity).'
                    </div>
                    <div class="w-15 fs-14 text-center">
                        '.(($equity>=$lastequity)?'<i class="fa fa-long-arrow-up text-success  me-4px fs-14 me-0" aria-hidden="true"></i>':'<i class="fa fa-long-arrow-down text-danger  ms-4px fs-14 me-0" aria-hidden="true"></i>').' 
                            
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mt-2">
                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/'.$date->toDateString().'/'.$id.'">
                    <p class="text-center m-0">Gap</p>
                    <div class="card-text d-flex text-dark m-0 text-center">
                        <div class="w-100 fs-14">
                            '.format_amount($gap).'
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mt-2">
                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/'.$date->toDateString().'/'.$id.'">
                    <p class="text-center m-0">Parking</p>
                    <div class="card-text d-flex text-dark m-0 text-center">
                        <div class="w-100 fs-14"> 
                            '.format_amount($parking).'
                        </div>
                    </div>
                </a>
            </div>
        </div>
    ';
    return $content;
}

function format_amount($number) {
    $decimal = '';
    if (strpos($number, '.') !== false) {
        list($number, $decimal) = explode('.', $number);
        $decimal = '.' . substr($decimal, 0, 2); // Limiting to 2 decimal places
    }
    
    $lastThree = substr($number, -3);
    $restUnits = substr($number, 0, -3);
    if(strlen($restUnits) > 0) {
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        $number = $restUnits . "," . $lastThree;
    } else {
        $number = $lastThree;
    }

    return $number . $decimal;
}