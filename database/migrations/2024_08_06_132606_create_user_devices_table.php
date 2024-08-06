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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address', 45); 
            $table->string('device_type')->nullable();
            $table->string('mac_id')->nullable();
            $table->integer('repeat')->nullable();
            $table->boolean('is_ip')->default(false);
            $table->boolean('is_mac')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
