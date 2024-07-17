<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientLogsTable extends Migration
{
    public function up()
    {
        Schema::create('client_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who made the change
            $table->string('field_name')->nullable(); // which field was changed
            $table->text('old_value')->nullable(); // old value
            $table->text('new_value')->nullable(); // new value
            $table->text('note')->nullable(); // additional notes
            $table->enum('log_type', ['update', 'note']); // type of log
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_logs');
    }
}
