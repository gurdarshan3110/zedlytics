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
            $table->unsignedBigInteger('ark_id')->nullable();
            $table->unsignedBigInteger('ticketOrderId')->nullable();
            $table->unsignedBigInteger('userId')->nullable();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->unsignedBigInteger('trxLogActionTypeId')->nullable();
            $table->unsignedBigInteger('trxLogTransTypeId')->nullable();
            $table->unsignedBigInteger('trxSubTypeId')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('openCommission', 15, 2)->nullable();
            $table->decimal('closeCommission', 15, 2)->nullable();
            $table->decimal('closePrice', 15, 2)->nullable();
            $table->string('method', 255)->nullable();
            $table->unsignedBigInteger('currencyId')->nullable();
            $table->string('currencyName', 255)->nullable();
            $table->decimal('closeProfit', 15, 2)->nullable();
            $table->unsignedBigInteger('openPositionId')->nullable();
            $table->decimal('closeRefCurrencyPrice', 15, 2)->nullable();
            $table->string('ipAddress', 255)->nullable();
            $table->timestamp('openPositionCreatedDate')->nullable();
            $table->longText('comment')->nullable();
            $table->unsignedBigInteger('createdById')->nullable();
            $table->timestamp('createdDate')->nullable();
            $table->decimal('openPolicyCommissionValue', 15, 2)->nullable();
            $table->unsignedBigInteger('openPolicyCommissionType')->nullable();
            $table->decimal('closePolicyCommissionValue', 15, 2)->nullable();
            $table->unsignedBigInteger('closePolicyCommissionType')->nullable();
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
