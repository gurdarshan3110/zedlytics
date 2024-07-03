<?php

namespace App\Http\Controllers\Auth;

use PragmaRX\Google2FALaravel\Support\Authenticator;
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
use Illuminate\Support\Facades\Http;
use Laravel\Fortify\Features;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        if (Auth::check()) {
            dd(Auth::user()->user_type)
            return redirect()->intended('/dashboard');
        }
        return view('auth.login');
    }

    public function twoFactor(){
        if (Auth::check()) {
            $title ="Two Factor Authentication";
            return view('twofactor.index',compact('title'));
        }
        return view('auth.login');
    }

    public function enable2Fa()
    {
        $response = Http::post('/user/two-factor-authentication');

        // Handle the response
        if ($response->successful()) {
            return $response->json();
        } else {
            // Handle error
            return response()->json(['error' => 'Request failed'], $response->status());
        }
    }

    // public function login(Request $request)
    // {
    //     // Validate the input
    //     $request->validate([
    //         'email' => 'required',
    //         'password' => 'required|min:4',
    //     ]);

    //     // Get the credentials from the request
    //     $credentials = $request->only('email', 'password');

    //     // Attempt to authenticate using the provided identifier
    //     $loginSuccess = false;

    //     // Check if identifier is email
    //     if (filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
    //         $loginSuccess = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']]);
    //     }
        
    //     // Check if identifier is phone number
    //     if (!$loginSuccess) {
    //         $loginSuccess = Auth::attempt(['phone_no' => $credentials['email'], 'password' => $credentials['password']]);
    //     }

    //     // Check if identifier is employee code
    //     if (!$loginSuccess) {
    //         $loginSuccess = Auth::attempt(['employee_code' => $credentials['email'], 'password' => $credentials['password']]);
    //     }

    //     // Check if login was successful
    //     if ($loginSuccess) {
    //         // Check if 2FA is enabled for the user
    //         $user = Auth::user();
    //         if (Auth::user()->two_factor_secret) {
    //             // 2FA is enabled, redirect to 2FA verification
    //             redirect()->route('two-factor-challenge');
    //         } else {
    //             // 2FA is not enabled, proceed to dashboard
    //             return $this->redirectToDashboard($user);
    //         }
    //     } else {
    //         // Authentication failed
    //         return redirect()->back()
    //             ->with('error','Invalid credentials')
    //             ->withInput();
    //     }
    // }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Determine if the input is a phone number, username, or email
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : (is_numeric($login) ? 'phone_number' : 'employee_code');

        // Attempt to authenticate the user
        if (Auth::attempt([$fieldType => $login, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended(config('fortify.home'));
        }

        throw ValidationException::withMessages([
            'login' => [trans('auth.failed')],
        ]);
    }

    protected function redirectTo2FA(Request $request, $user)
    {
        // Generate and store the secret key for the user
        $google2fa_url = (new Authenticator)->generateSecretKey();
        $user->google2fa_secret = $google2fa_url['secret'];
        $user->save();

        // Redirect to the 2FA verification page
        return redirect('2fa')->with([
            'user' => $user,
            'google2fa_url' => $google2fa_url,
        ]);
    }

    protected function redirectToDashboard($user)
    {
        // Define the user types that are allowed to login
        $permissions = permissions();


        // Check if user type is allowed to access dashboard
        if (in_array('dashboard', $permissions)) {
            return redirect()->intended('/dashboard');
        } else if (in_array('employee dashboard', $permissions)) { 
            return redirect()->intended('/employee-dashboard');
        }else{
            return redirect()->intended('/open-positions');
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
