<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComingSoonController extends Controller
{
    public function index()
    {
        $title = 'Coming Soon';
        return view('coming-soon.index',compact('title'));
    }
}
