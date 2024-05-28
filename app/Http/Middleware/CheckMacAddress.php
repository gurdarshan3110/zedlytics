<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MacAddress;
use App\Models\User;

class CheckMacAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        //dd(Auth::user());

        $bypassRoutes = [
            'login',
            'register',
            'password/reset',
            'logout',
            '/'
            // Add other routes or URIs you want to bypass
        ];
        if ($user && $user->user_type == User::USER_SUPER_ADMIN) {
            return $next($request);
        }
        // Check if the request path should bypass the MAC address check
        if ($this->shouldBypass($request, $bypassRoutes)) {
            return $next($request);
        }
        //$macAddress = $request->header('X-MAC-Address');
        $macAddress = $request->ip();
        //dd($user);
        if (!$user || !$macAddress) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $allowedMacAddresses = MacAddress::where('user_id', $user->id)->pluck('mac_address')->toArray();

        if (!in_array($macAddress, $allowedMacAddresses)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    protected function shouldBypass(Request $request, array $bypassRoutes)
    {
        foreach ($bypassRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }
}

