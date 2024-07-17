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
        'liquidated',
        'status',
    ];

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
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'NA',
        ]);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }




}
