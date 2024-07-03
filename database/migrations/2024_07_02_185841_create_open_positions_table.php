<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('ticketID')->unique();
            $table->integer('userID');
            $table->unsignedBigInteger('posCurrencyID');
            $table->timestamp('posDate');
            $table->decimal('openAmount', 15, 2);
            $table->decimal('closeAmount', 15, 2);
            $table->decimal('posPrice', 15, 2);
            $table->integer('posType');
            $table->decimal('openCommission', 15, 2);
            $table->decimal('currentPrice', 15, 2);
            $table->integer('referenceCurrencyId');
            $table->text('posComment')->nullable();
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
        Schema::dropIfExists('open_positions');
    }
}

