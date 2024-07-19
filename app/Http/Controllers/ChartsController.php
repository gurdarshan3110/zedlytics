<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ChartsController extends Controller
{
    const TITLE = 'ZL Charts';
    const TITLE1 = 'Financials Calendar';
    const URL = 'charts';
    const URL1 = 'financial-calendar';
    const DIRECTORY = 'charts';
    const DIRECTORY1 = 'financialcalendar';
    const FNAME = 'ZL Charts';
    const FNAME1 = 'Financial Calendar';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        //if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory'));
        // }else{
        //     return redirect()->route('dashboard.index');
        // }
    }

    public function findex()
    {
        $title = self::TITLE1;
        $url = self::URL1;
        $directory = self::DIRECTORY1;
        //if(in_array('view '.$url,permissions())){
            return view($directory.'.index', compact('title','url','directory'));
        // }else{
        //     return redirect()->route('dashboard.index');
        // }
    }    
}
