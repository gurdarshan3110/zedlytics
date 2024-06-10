<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquityRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'equity',
        'deposit',
        'withdraw',
        'ledger_date',
        'user_id',
        'brand_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Add any other necessary methods or relationships here
}
