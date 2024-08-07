<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use TwoFactorAuthenticatable, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public const USER_SUPER_ADMIN = 'super_admin';

    public const USER_EMPLOYEE = 'employee';

    public const USER_CLIENT = 'client';

    protected $fillable = [
        'name',
        'email',
        'employee_code',
        'password',
        'phone_no',
        'user_type',
        'role',
        'ip_auth'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'user_employees');
    }

    public function macAddresses()
    {
        return $this->hasMany(MacAddress::class);
    }


}
