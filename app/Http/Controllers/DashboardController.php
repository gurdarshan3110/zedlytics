<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    const TITLE = 'Dashboard';

    public function index()
    {
        // Your logic to retrieve data for the dashboard goes here
        $title = self::TITLE;
        return view('dashboard.index',compact('title'));
    }
}
