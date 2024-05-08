<?php
// app/Models/UserEmployee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmployee extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
    ];
}
