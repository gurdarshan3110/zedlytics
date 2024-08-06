<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'device_type',
        'mac_id',
        'repeat',
        'is_ip',
        'is_mac',
    ];

    /**
     * Get the user that owns the device.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
