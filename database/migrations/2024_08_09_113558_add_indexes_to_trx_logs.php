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
        Schema::table('trx_logs', function (Blueprint $table) {
            $table->index('createdDate');
            $table->index('userId');
            $table->index('accountId');
            $table->index('currencyId');

            $table->index('closeProfit');
            $table->index('openCommission');
            $table->index('closeCommission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_logs', function (Blueprint $table) {
            $table->dropIndex(['createdDate']);
            $table->dropIndex(['userId']);
            $table->dropIndex(['accountId']);
            $table->dropIndex(['currencyId']);

            $table->dropIndex(['closeProfit']);
            $table->dropIndex(['openCommission']);
            $table->dropIndex(['closeCommission']);
        });
    }
};
