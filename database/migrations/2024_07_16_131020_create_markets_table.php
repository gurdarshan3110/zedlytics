<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('margin_limit_markets', function (Blueprint $table) {
            $table->id();
            $table->string('market');
            $table->string('script');
            $table->string('minimum_deal');
            $table->string('maximum_deal_in_single_order');
            $table->string('maximum_quantity_in_script');
            $table->string('intraday_margin');
            $table->string('holding_maintainence_margin');
            $table->string('inventory_day_margin');
            $table->string('total_group_limit');
            $table->string('margin_calculation_time');

            $table->boolean('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('margin_limit_markets');
    }
};
