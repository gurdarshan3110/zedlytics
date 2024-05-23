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

    public static function generateEmployeeCode()
    {
        // Get the last employee code
        $lastEmployee = self::withTrashed()->orderBy('employee_code', 'desc')->first();

        if ($lastEmployee) {
            $lastCode = $lastEmployee->employee_code;
            $numericPart = intval(substr($lastCode, 1));
            $newCode = 'S' . str_pad($numericPart + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // If there are no employees yet, start with S001
            $newCode = 'S001';
        }

        return $newCode;
    }

}
