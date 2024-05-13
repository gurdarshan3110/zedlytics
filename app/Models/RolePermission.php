<?php
// app/Models/RolePermission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_has_permissions';

    public $timestamps = false; // Assuming the pivot table doesn't have timestamps

    protected $primaryKey = ['permission_id', 'role_id'];

    public $incrementing = false; // Assuming the primary key is not auto-incrementing

    protected $fillable = [
        'permission_id', 'role_id',
    ];

    // Define relationships if needed
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
