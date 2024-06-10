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
        Schema::create('equity_records', function (Blueprint $table) {
            $table->id();
            $table->decimal('equity', 10, 2)->nullable(); 
            $table->decimal('deposit', 10, 2)->nullable(); 
            $table->decimal('withdraw', 10, 2)->nullable(); 
            $table->date('ledger_date')->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->boolean('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equity_records');
    }
};
