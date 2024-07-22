<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarginLimitMarket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'margin_limit_markets'; // Specify the table name if not following conventions

    protected $fillable = [
        'brand_id',
        'market',
        'script',
        'minimum_deal',
        'maximum_deal_in_single_order',
        'maximum_quantity_in_script',
        'intraday_margin',
        'holding_maintainence_margin',
        'inventory_day_margin',
        'total_group_limit',
        'margin_calculation_time',
        'status',
    ];

}
