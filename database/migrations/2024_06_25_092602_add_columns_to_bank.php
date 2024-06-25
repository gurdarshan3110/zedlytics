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
        Schema::table('banks', function (Blueprint $table) {
            $table->string('ifsc')->nullable();
            $table->string('branch')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->decimal('commission_rate', 10, 2)->nullable();
            $table->decimal('lean_balance', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropColumn('ifsc');
            $table->dropColumn('branch');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('commission_rate');
            $table->dropColumn('lean_balance');
        });
    }
};
