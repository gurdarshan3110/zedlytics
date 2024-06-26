<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $table = 'withdraw_requests';

    protected $fillable = [
        'comment',
        'request_status',
        'amount',
        'branch_id',
        'request_date',
        'user_id',
        'request_id',
        'status',
    ];

    public $timestamps = true;
}
