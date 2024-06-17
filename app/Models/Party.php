<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use HasFactory, SoftDeletes;

    public const POOL_TYPE_ZERO = 0;
    public const BANK_TYPE_ZERO = 1;

    protected $fillable = [
        'account_code',
        'name',
        'description',
        'type',
        'status',
    ];

    public function accounts()
    {
        return $this->hasMany(PartyAccount::class);
    }

}
