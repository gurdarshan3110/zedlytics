<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use App\Models\ActivityLog;
use Carbon\Carbon;

class LogUserLogin
{
    public function handle(Authenticated $event)
    {
        $log = ActivityLog::where('user_id', $event->user->id)
                ->whereNull('end_time')
                ->latest('start_time')
                ->first();
        if($log==null){
            ActivityLog::create([
                'user_id' => $event->user->id,
                'start_time' => Carbon::now(),
            ]);
        }
    }
}
