<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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
