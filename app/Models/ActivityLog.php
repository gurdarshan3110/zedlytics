<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'start_time', 'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate total hours of a user between two dates.
     *
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function calculateTotalHours($userId, $startDate, $endDate)
    {
        $totalHours = self::where('user_id', $userId)
            ->where('start_time','>=',$startDate)
            ->where('end_time','>=',$endDate)
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, start_time, IFNULL(end_time, NOW())) / 3600) as hours'))
            ->value('hours');

        return $totalHours ?? 0;
    }
}

