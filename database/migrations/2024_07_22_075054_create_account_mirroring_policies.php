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
        Schema::create('account_mirroring_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ark_id');
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
        Schema::dropIfExists('account_mirroring_policies');
    }
};
