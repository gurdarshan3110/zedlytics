<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/'; // Customize the redirect path after password reset

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Override the showResetForm method to pass additional data to the view
    protected function showResetForm(Request $request, $email, $token)
    {
        return view('auth.passwords.resets')->with(
            ['token' => $token, 'email' => $email]
        );
    }

    public function resetPass(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
            'token' => 'required',
        ]);
        //dd($request);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return back()->with('status', 'Password reset successfully. You can now log in.');
        } else {
            return back()->withErrors(['email' => [__($status)]]);
        }
    }
}
