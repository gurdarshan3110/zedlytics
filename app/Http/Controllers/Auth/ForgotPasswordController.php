<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/'; // Customize the redirect path after password reset

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Override the showResetForm method to pass additional data to the view
    
}
