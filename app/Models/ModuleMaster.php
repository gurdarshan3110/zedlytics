<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'url',
        'parent',
        'sno',
        'status',
    ];
}
