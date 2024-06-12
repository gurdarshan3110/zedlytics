<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'name',
        'description',
        'status',
    ];

    public function accounts()
    {
        return $this->hasMany(PartyAccount::class);
    }

}
