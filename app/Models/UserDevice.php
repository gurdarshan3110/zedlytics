<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_address',
        'address_type',
        'device_type',
        'is_available',
        'reason',
        'updated_by',
    ];

    public function getCountAttribute()
    {
        return static::where('client_address', $this->client_address)->count();
    }

    /**
     * Get the user that owns the device.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'user_id', 'user_id');
    }
}
