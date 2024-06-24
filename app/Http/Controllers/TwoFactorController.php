<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    const TITLE = 'Two Factor Authentication';
    const URL = 'two-factor';
    const DIRECTORY = 'twofactor';
    const FNAME = 'Two Factor Authentication';

    public function index()
    {
        $title = self::TITLE;
        $url = self::URL;
        $directory = self::DIRECTORY;
        return view($directory.'.index', compact('title','url','directory'));
    }
}
