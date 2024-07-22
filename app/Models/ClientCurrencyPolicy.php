<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCurrencyPolicy extends Model
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
        return $this->belongsTo(ClientCurrencyPolicy::class, 'parentId');
    }

    public function childPolicies()
    {
        return $this->hasMany(ClientCurrencyPolicy::class, 'parentId');
    }
}
