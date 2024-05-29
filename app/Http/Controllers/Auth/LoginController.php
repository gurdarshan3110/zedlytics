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
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

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

    public function login(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:4',
        ]);

        // Get the credentials from the request
        $credentials = $request->only('email', 'password');

        // Define the user types that are allowed to login
        $userTypes = [User::USER_SUPER_ADMIN, User::USER_EMPLOYEE];

        // Attempt to authenticate using the provided identifier
        $loginSuccess = false;

        // Check if identifier is email
        if (filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $loginSuccess = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']]);
        }
        
        // Check if identifier is phone number
        if (!$loginSuccess) {
            $loginSuccess = Auth::attempt(['phone_no' => $credentials['email'], 'password' => $credentials['password']]);
        }

        // Check if identifier is employee code
        if (!$loginSuccess) {
            $loginSuccess = Auth::attempt(['employee_code' => $credentials['email'], 'password' => $credentials['password']]);
        }

        // Check if login was successful and user has the correct user type
        if ($loginSuccess && in_array(Auth::user()->user_type, $userTypes)) {
            return redirect()->intended('/dashboard');
        } else {
            // Authentication failed
            return redirect()->back()
                    ->with('error','Invalid credentials')
                    ->withInput();
        }
    }

    // Process the login form
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //     ]);
    //     $userTypes = [User::USER_SUPER_ADMIN,User::USER_EMPLOYEE,User::USER_CLIENT]; 
    //     if (Auth::attempt($credentials) && in_array(Auth::user()->user_type, $userTypes)) {
    //         return redirect()->intended('/dashboard');
    //     } else {
    //         // Authentication failed
    //         return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    //     }
    // }

    // Logout the authenticated user

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Find the latest activity log entry that has no end_time
            $lastLog = ActivityLog::where('user_id', $user->id)
                ->whereNull('end_time')
                ->latest('start_time')
                ->first();
            //dd($lastLog);
            // Update the end_time
            if ($lastLog) {
                $lastLog->end_time = Carbon::now();
                $lastLog->save();
                
            } 
        }

        Auth::logout();
        //$request->session()->invalidate();
        //$request->session()->regenerateToken();

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
