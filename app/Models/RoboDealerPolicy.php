<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoboDealerPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policyName',
        'policyTypeId',
        'parentId',
    ];

    // Define any relationships if necessary
    public function parentPolicy()
    {
        return $this->belongsTo(RoboDealerPolicy::class, 'parentId');
    }

    public function childPolicies()
    {
        return $this->hasMany(RoboDealerPolicy::class, 'parentId');
    }
}
