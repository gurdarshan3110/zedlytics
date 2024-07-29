<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxLog extends Model
{
    use HasFactory;

    protected $table = 'trx_logs';

    protected $fillable = [
        'ark_id',
        'ticketOrderId',
        'userId',
        'accountId',
        'amount',
        'trxLogActionTypeId',
        'trxLogTransTypeId',
        'trxSubTypeId',
        'price',
        'openCommission',
        'closeCommission',
        'closePrice',
        'method',
        'currencyId',
        'currencyName',
        'closeProfit',
        'openPositionId',
        'closeRefCurrencyPrice',
        'ipAddress',
        'openPositionCreatedDate',
        'comment',
        'createdById',
        'createdDate',
        'openPolicyCommissionValue',
        'openPolicyCommissionType',
        'closePolicyCommissionValue',
        'closePolicyCommissionType',
    ];

    protected $casts = [
        'openPositionCreatedDate' => 'datetime',
        'createdDate' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'userId','user_id');
    }

}
