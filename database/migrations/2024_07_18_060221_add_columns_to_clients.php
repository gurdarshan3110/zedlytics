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
            $table->string('city');
            $table->string('district');
            $table->string('state');
            $table->string('first_language');
            $table->string('second_language');
            $table->string('third_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('district');
            $table->dropColumn('state');
            $table->dropColumn('first_language');
            $table->dropColumn('second_language');
            $table->dropColumn('third_language');
        });
    }
};
