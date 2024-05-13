<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    protected $table = 'model_has_roles';
    protected $fillable = [
        'model_type', 'model_id', 'role_id',
    ];
}