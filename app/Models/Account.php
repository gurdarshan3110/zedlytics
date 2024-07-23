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

    protected $appends = ['username','client_account_code'];

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

    public function getUsernameAttribute()
    {
        if ($this->type === self::CLIENT_ACCOUNT) {
            $clientAccount = $this->clientAccounts()->first();
            if ($clientAccount && $clientAccount->client) {
                return $clientAccount->client->username;
            }
        }else if($this->type === self::BANK_ACCOUNT) {
            $bankAccount = $this->bankAccounts()->first();
            if ($bankAccount && $bankAccount->bank) {
                return $bankAccount->bank->name;
            }

        }else {
            $partyAccount = $this->partyAccounts()->first();
            if ($partyAccount && $partyAccount->party) {
                return $partyAccount->party->name;
            }

        }
        return '';
    }

    public function getClientAccountCodeAttribute()
    {
        if ($this->type === self::CLIENT_ACCOUNT) {
            $clientAccount = $this->clientAccounts()->first();
            if ($clientAccount && $clientAccount->client) {
                return $this->account_code.' | '.$clientAccount->client->username;
            }
        }
        return '';
    }

}
