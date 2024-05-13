<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    public const CLIENT_ACCOUNT = 'client';

    public const BANK_ACCOUNT = 'bank';

    public const PARTY_ACCOUNT = 'party';

    public const BRAND_ACCOUNT = 'brand';

    protected $fillable = [
        'account_code',
        'name',
        'type',
        'status',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function banks()
    {
        return $this->belongsToMany(Bank::class, 'bank_accounts');
    }

    public function partyAccounts()
    {
        return $this->hasMany(PartyAccount::class);
    }

    public function parties()
    {
        return $this->belongsToMany(Party::class, 'party_accounts');
    }

    public function brandAccounts()
    {
        return $this->hasMany(BrandAccount::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_accounts');
    }

    public function clientAccounts()
    {
        return $this->hasMany(ClientAccount::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_accounts');
    }

}
