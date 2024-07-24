<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID');
            $table->unsignedBigInteger('currenciesPoliciesID');
            $table->unsignedBigInteger('genericPoliciesID');
            $table->timestamp('openDate');
            $table->unsignedBigInteger('createdBy');
            $table->string('country');
            $table->timestamp('termsAcceptedDate')->nullable();
            $table->boolean('ignoreLiquidation')->default(false);
            $table->boolean('closeOnly')->default(false);
            $table->boolean('openOnly')->default(false);
            $table->string('firstName');
            $table->string('username');
            $table->unsignedTinyInteger('userType');
            $table->unsignedTinyInteger('tradingType');
            $table->unsignedInteger('blockFrequentTradesSeconds')->default(0);
            $table->boolean('validateMoneyBeforeEntry')->default(true);
            $table->boolean('validateMoneyBeforeClose')->default(false);
            $table->boolean('clientPriceExecution')->default(false);
            $table->decimal('creditLoanPercentage', 5, 2)->default(0);
            $table->unsignedBigInteger('parentId');
            $table->boolean('enableCashDelivery')->default(false);
            $table->boolean('enableDepositRequest')->default(false);
            $table->unsignedTinyInteger('accountType');
            $table->boolean('locked')->default(false);
            $table->boolean('liquidated')->default(false);
            $table->boolean('termsAccepted')->default(false);
            $table->boolean('allowMultiSession')->default(false);
            $table->boolean('isDemo')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealers');
    }
}
