<?php
use App\Models\ModuleMaster;
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