<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_currencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_id')->index(); 
            $table->string('name');
            $table->boolean('used')->default(false);
            $table->integer('open_day');
            $table->integer('close_day');
            $table->time('open_time');
            $table->time('close_time');
            $table->time('daily_close_time_from1');
            $table->time('daily_close_time_to1');
            $table->time('daily_close_time_from2');
            $table->time('daily_close_time_to2');
            $table->time('daily_close_time_from3');
            $table->time('daily_close_time_to3');
            $table->integer('tick_digits');
            $table->boolean('closed')->default(false);
            $table->integer('reference_currency_id');
            $table->integer('decimal_digits');
            $table->boolean('sell_only')->default(false);
            $table->boolean('buy_only')->default(false);
            $table->text('description')->nullable();
            $table->integer('currency_type_id');
            $table->integer('parent_id');
            $table->integer('amount_unit_id');
            $table->string('row_color');
            $table->boolean('auto_stop_trade')->default(false);
            $table->integer('auto_stop_trade_seconds')->default(0);
            $table->boolean('requotable')->default(false);
            $table->boolean('move_if_closed')->default(false);
            $table->boolean('spread_from_bid')->default(false);
            $table->string('feeder_name')->nullable();
            $table->datetime('expiry_date')->nullable();
            $table->integer('contract_size');
            $table->boolean('direct_calculation')->default(false);
            $table->boolean('ref_direct_calculation')->default(false);
            $table->boolean('close_cancel_all_on_expiry')->default(true);
            $table->boolean('auto_cancel_sltp_orders')->default(true);
            $table->boolean('auto_cancel_entry_orders')->default(true);
            $table->integer('auto_switch_feed_seconds')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base_currencies');
    }
}
