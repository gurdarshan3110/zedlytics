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
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('currenciesPoliciesID')->nullable();
            $table->unsignedBigInteger('genericPoliciesID')->nullable();
            $table->timestamp('openDate')->nullable();
            $table->unsignedBigInteger('createdBy')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('termsAcceptedDate')->nullable();
            $table->ipAddress('termsAcceptedIP')->nullable();
            $table->boolean('ignoreLiquidation')->default(false);
            $table->boolean('closeOnly')->default(false);
            $table->boolean('openOnly')->default(false);
            $table->unsignedTinyInteger('tradingType')->nullable();
            $table->unsignedInteger('blockFrequentTradesSeconds')->default(0);
            $table->boolean('validateMoneyBeforeEntry')->default(false);
            $table->boolean('validateMoneyBeforeClose')->default(false);
            $table->boolean('clientPriceExecution')->default(false);
            $table->float('percentageLevel1', 8, 2)->nullable();
            $table->float('percentageLevel2', 8, 2)->nullable();
            $table->float('percentageLevel3', 8, 2)->nullable();
            $table->float('percentageLevel4', 8, 2)->nullable();
            $table->float('creditLoanPercentage', 8, 2)->nullable();
            $table->unsignedBigInteger('parentId')->nullable();
            $table->string('currencySign')->nullable();
            $table->string('accountIdPrefix')->nullable();
            $table->boolean('enableCashDelivery')->default(false);
            $table->boolean('enableDepositRequest')->default(false);
            $table->unsignedTinyInteger('accountType')->nullable();
            $table->boolean('isDemo')->default(true);
            $table->boolean('allowMultiSession')->default(false);
            $table->boolean('termsAccepted')->default(true);
            $table->boolean('liquidated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'currenciesPoliciesID',
                'genericPoliciesID',
                'openDate',
                'createdBy',
                'country',
                'termsAcceptedDate',
                'termsAcceptedIP',
                'ignoreLiquidation',
                'closeOnly',
                'openOnly',
                'tradingType',
                'blockFrequentTradesSeconds',
                'validateMoneyBeforeEntry',
                'validateMoneyBeforeClose',
                'clientPriceExecution',
                'percentageLevel1',
                'percentageLevel2',
                'percentageLevel3',
                'percentageLevel4',
                'creditLoanPercentage',
                'parentId',
                'currencySign',
                'accountIdPrefix',
                'enableCashDelivery',
                'enableDepositRequest',
                'accountType',
                'isDemo',
                'allowMultiSession',
                'termsAccepted',
                'liquidated',
            ]);
        });
    }
};
