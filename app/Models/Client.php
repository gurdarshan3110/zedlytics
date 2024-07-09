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
        'user_id',
        'username',
        'name',
        'phone_no',
        'email',
        'status',
    ];

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'user_clients');
    // }
    public function accounts()
    {
        return $this->hasMany(ClientAccount::class);
    }
}
