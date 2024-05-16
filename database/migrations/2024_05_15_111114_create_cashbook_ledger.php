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
        Schema::create('cashbook_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->nullable();
            $table->integer('account_id')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('utr_no')->nullable();
            $table->decimal('amount', 10, 2)->nullable(); 
            $table->boolean('type')->default(0); 
            $table->decimal('balance', 10, 2)->nullable(); 

            $table->integer('employee_id')->nullable();
            $table->date('ledger_date')->nullable(); 

            $table->boolean('status')->default(0); 
            $table->string('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashbook_ledger');
    }
};
