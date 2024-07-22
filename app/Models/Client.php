<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_code',
        'user_id',
        'brand_id',
        'username',
        'name',
        'phone_no',
        'mobile',
        'email',
        'rm',
        'status',
        'currenciesPoliciesID',
        'genericPoliciesID',
        'openDate',
        'createdBy',
        'country',
        'termsAcceptedDate',
        'termsAcceptedIP',
        'ignoreLiquidation',
        'closeOnly',
        'openOnly',
        'tradingType',
        'blockFrequentTradesSeconds',
        'validateMoneyBeforeEntry',
        'validateMoneyBeforeClose',
        'clientPriceExecution',
        'percentageLevel1',
        'percentageLevel2',
        'percentageLevel3',
        'percentageLevel4',
        'creditLoanPercentage',
        'parentId',
        'currencySign',
        'accountIdPrefix',
        'enableCashDelivery',
        'enableDepositRequest',
        'accountType',
        'isDemo',
        'allowMultiSession',
        'termsAccepted',
        'city',
        'district',
        'state',
        'first_language',
        'second_language',
        'third_language',
        'liquidated',
        'status',
    ];

    protected $append = ['currency_policies_names','generic_policies_name','robo_policies_name','account_mirroring_policies_name'];

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'user_clients');
    // }

    public function accounts()
    {
        return $this->hasMany(ClientAccount::class);
    }

    public function rmanager()
    {
        return $this->belongsTo(User::class,'rm');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function logs()
    {
        return $this->hasMany(ClientLog::class)->orderBy('id', 'desc');
    }

    public function currencyPolicy()
    {
        return $this->hasMany(ClientCurrencyPolicy::class, 'ark_id', 'currenciesPoliciesID');
    }

    public function getCurrencyPolicyNamesAttribute()
    {
        return $this->currencyPolicy()->pluck('policyName')->implode(', ');
    }

    public function genericPolicy()
    {
        return $this->hasMany(ClientGenericPolicy::class, 'ark_id', 'genericPoliciesID');
    }

    public function getGenericNamesAttribute()
    {
        return $this->genericPolicy()->pluck('policyName')->implode(', ');
    }

}
