<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrackActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastActivity = session('last_activity_time');
            $currentTime = Carbon::now();

            // Log user activity only if not on login route
            if (!$request->is('login') && !$request->is('logout')) {
                // If last activity was more than 15 minutes ago, update end_time of the last log
                if ($lastActivity) {
                    $inactiveTime = $currentTime->diffInMinutes(Carbon::parse($lastActivity));
                    if ($inactiveTime >= 15) {
                        ActivityLog::where('user_id', $user->id)
                            ->whereNull('end_time')
                            ->update(['end_time' => Carbon::parse($lastActivity)]);
                        
                        
                        Auth::logout();
                        session()->invalidate();
                        session()->regenerateToken();

                        return redirect('/login')->withErrors(['message' => 'You have been logged out due to inactivity.']);
                    }
                }

                // Check if there's an active log entry for the user
                $activeLog = ActivityLog::where('user_id', $user->id)
                    ->whereNull('end_time')
                    ->first();

                if (!$activeLog) {
                    // Create a new activity log entry
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'start_time' => $currentTime,
                    ]);
                    
                }

                // Update last activity time in session
                session(['last_activity_time' => $currentTime]);
            }
        }

        return $next($request);
    }
}
