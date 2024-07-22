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
        Schema::create('client_currency_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policyName');
            $table->unsignedBigInteger('policyTypeId');
            $table->unsignedBigInteger('parentId')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_currency_policies');
    }
};
