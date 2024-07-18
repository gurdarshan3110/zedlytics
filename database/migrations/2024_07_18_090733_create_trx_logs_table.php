<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticketOrderId');
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('accountId');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('trxLogActionTypeId');
            $table->unsignedBigInteger('trxLogTransTypeId');
            $table->unsignedBigInteger('trxSubTypeId');
            $table->decimal('price', 15, 2);
            $table->decimal('openCommission', 15, 2);
            $table->decimal('closeCommission', 15, 2);
            $table->decimal('closePrice', 15, 2);
            $table->string('method', 255);
            $table->unsignedBigInteger('currencyId');
            $table->string('currencyName', 255);
            $table->decimal('closeProfit', 15, 2);
            $table->unsignedBigInteger('openPositionId');
            $table->decimal('closeRefCurrencyPrice', 15, 2);
            $table->string('ipAddress', 255);
            $table->timestamp('openPositionCreatedDate');
            $table->longText('comment')->nullable();
            $table->unsignedBigInteger('createdById');
            $table->timestamp('createdDate');
            $table->decimal('openPolicyCommissionValue', 15, 2);
            $table->unsignedBigInteger('openPolicyCommissionType');
            $table->decimal('closePolicyCommissionValue', 15, 2);
            $table->unsignedBigInteger('closePolicyCommissionType');
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
        Schema::dropIfExists('trx_logs');
    }
}
