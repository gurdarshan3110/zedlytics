<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'brand_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
