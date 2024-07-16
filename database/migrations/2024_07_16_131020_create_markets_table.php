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
            $table->integer('minimum_deal');
            $table->integer('maximum_deal_in_single_order');
            $table->integer('maximum_quantity_in_script');
            $table->decimal('intraday_margin', 8, 2);
            $table->decimal('holding_maintainence_margin', 8, 2);
            $table->decimal('inventory_day_margin', 8, 2);
            $table->decimal('total_group_limit', 8, 2);
            $table->time('margin_calculation_time');

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
