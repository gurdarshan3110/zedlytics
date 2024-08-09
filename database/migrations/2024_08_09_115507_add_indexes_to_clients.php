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
            $table->index('user_id');
            $table->index('client_code');
            $table->index('parentId');
            $table->index('phone_no');
            $table->index('username');
            $table->index('createdBy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['client_code']);
            $table->dropIndex(['parentId']);
            $table->dropIndex(['phone_no']);
            $table->dropIndex(['username']);
            $table->dropIndex(['createdBy']);
        });
    }
};
