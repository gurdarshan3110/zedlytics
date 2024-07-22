<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientGenericPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'ark_id',
        'policyName',
    ];

}
