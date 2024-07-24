<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'userID',
        'currenciesPoliciesID',
        'genericPoliciesID',
        'openDate',
        'createdBy',
        'country',
        'termsAcceptedDate',
        'ignoreLiquidation',
        'closeOnly',
        'openOnly',
        'firstName',
        'username',
        'userType',
        'tradingType',
        'blockFrequentTradesSeconds',
        'validateMoneyBeforeEntry',
        'validateMoneyBeforeClose',
        'clientPriceExecution',
        'creditLoanPercentage',
        'parentId',
        'enableCashDelivery',
        'enableDepositRequest',
        'accountType',
        'locked',
        'liquidated',
        'termsAccepted',
        'allowMultiSession',
        'isDemo',
    ];

    protected $casts = [
        'openDate' => 'datetime',
        'termsAcceptedDate' => 'datetime',
        'ignoreLiquidation' => 'boolean',
        'closeOnly' => 'boolean',
        'openOnly' => 'boolean',
        'validateMoneyBeforeEntry' => 'boolean',
        'validateMoneyBeforeClose' => 'boolean',
        'clientPriceExecution' => 'boolean',
        'enableCashDelivery' => 'boolean',
        'enableDepositRequest' => 'boolean',
        'locked' => 'boolean',
        'liquidated' => 'boolean',
        'termsAccepted' => 'boolean',
        'allowMultiSession' => 'boolean',
        'isDemo' => 'boolean',
    ];
}
