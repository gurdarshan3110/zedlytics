<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseCurrency extends Model
{
    use HasFactory;

    protected $table = 'base_currencies';

    protected $fillable = [
        'base_id','name', 'used', 'open_day', 'close_day', 'open_time', 'close_time', 
        'daily_close_time_from1', 'daily_close_time_to1', 'daily_close_time_from2', 
        'daily_close_time_to2', 'daily_close_time_from3', 'daily_close_time_to3', 
        'tick_digits', 'closed', 'reference_currency_id', 'decimal_digits', 'sell_only', 
        'buy_only', 'description', 'currency_type_id', 'parent_id', 'amount_unit_id', 
        'row_color', 'auto_stop_trade', 'auto_stop_trade_seconds', 'requotable', 
        'move_if_closed', 'spread_from_bid', 'feeder_name', 'expiry_date', 'contract_size', 
        'direct_calculation', 'ref_direct_calculation', 'close_cancel_all_on_expiry', 
        'auto_cancel_sltp_orders', 'auto_cancel_entry_orders', 'auto_switch_feed_seconds'
    ];

    protected $appends = [
        'parent','child_ids'
    ];

    public function positions()
    {
        return $this->hasMany(OpenPosition::class, 'posCurrencyID');
    }

    public function getParentAttribute()
    {
        if($this->parent_id==null){
            return null;
        }
        return BaseCurrency::select('name')->where('base_id', $this->parent_id)->first()->name;
    }

    public function getChildIdsAttribute()
    {
        return $this->hasMany(BaseCurrency::class, 'parent_id', 'base_id')->pluck('base_id');
    }

    public function childCurrencies()
    {
        return $this->hasMany(BaseCurrency::class, 'parent_id', 'base_id');
    }

    public function childTransactions()
    {
        return $this->hasManyThrough(TrxLog::class, BaseCurrency::class, 'parent_id', 'currencyId', 'base_id', 'base_id');
    }

    public function trxLogs()
    {
        return $this->hasMany(TrxLog::class, 'currencyId', 'base_id');
    }

}
