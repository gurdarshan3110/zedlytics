<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Store;
use App\Models\Agent;
use App\Models\Agency;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        if (Auth::check()) {
            
            return redirect()->intended('/dashboard');
        }
        return view('auth.login');
    }

    // Process the login form
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        $userTypes = [User::USER_SUPER_ADMIN,User::USER_EMPLOYEE,User::USER_CLIENT]; 
        if (Auth::attempt($credentials) && in_array(Auth::user()->user_type, $userTypes)) {
            return redirect()->intended('/dashboard');
        } else {
            // Authentication failed
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }
    }

    // Logout the authenticated user
    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }

    
    public function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generateRandomNo($name) {
        // Convert name to uppercase
        $name = strtoupper($name);
        
        // Get the first three letters from the name
        $letters = substr($name, 0, 3);
        
        // Generate three random numbers
        $numbers = mt_rand(100, 999);
        
        // Combine letters and numbers
        $randomString = $letters . $numbers;
        
        return $randomString;
    }

}
