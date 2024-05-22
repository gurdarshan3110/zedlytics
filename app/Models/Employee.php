<?php

// app/Models/Employee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_code',
        'name',
        'phone_no',
        'email',
        'status',
        'role',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_employees');
    }
}
