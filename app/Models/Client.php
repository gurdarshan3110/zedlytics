<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_code',
        'name',
        'phone_no',
        'email',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_clients');
    }
}
