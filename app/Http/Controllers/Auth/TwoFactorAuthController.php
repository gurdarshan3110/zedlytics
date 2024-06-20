<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorAuthController extends Controller
{
    public function show2faForm()
    {
        return view('auth.2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            '2fa_code' => 'required',
        ]);

        $user = Auth::user();

        // Validate the 2FA code
        $valid = (new Authenticator)->verifyKey($user->google2fa_secret, $request->input('2fa_code'));

        if ($valid) {
            // Mark the user as authenticated
            Auth::login($user);

            // Redirect to intended location
            return $this->redirectToDashboard($user);
        } else {
            // Invalid 2FA code
            return redirect()->back()->with('error', 'Invalid 2FA code. Please try again.');
        }
    }

    protected function redirectToDashboard($user)
    {
        // Define the user types that are allowed to login
        $userTypes = [User::USER_SUPER_ADMIN, User::USER_EMPLOYEE];

        // Check if user type is allowed to access dashboard
        if (in_array($user->user_type, $userTypes)) {
            return redirect()->intended('/dashboard');
        } else {
            return redirect()->intended('/employee-dashboard');
        }
    }
}
