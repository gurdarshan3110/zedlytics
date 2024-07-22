<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountMirroringPolicy extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ark_id',
        'policyName',
        'policyTypeId',
        'parentId',
    ];

    public function parentPolicy()
    {
        return $this->belongsTo(AccountMirroringPolicy::class, 'parentId');
    }

    public function childPolicies()
    {
        return $this->hasMany(AccountMirroringPolicy::class, 'parentId');
    }
}
