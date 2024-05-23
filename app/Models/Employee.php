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

    public static function generateEmployeeCode($name)
    {
        // Get the first letter of the name (convert to uppercase)
        $firstLetter = strtoupper(substr($name, 0, 1));

        // Get the last employee code starting with the same letter
        $lastEmployee = self::withTrashed()
            ->where('employee_code', 'like', $firstLetter . '%')
            ->orderBy('employee_code', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastCode = $lastEmployee->employee_code;
            $numericPart = intval(substr($lastCode, 1));
            $newCode = $firstLetter . str_pad($numericPart + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // If there are no employees with the same initial letter, start with 001
            $newCode = $firstLetter . '001';
        }

        return $newCode;
    }

}
