<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRecurrenceDaysTable extends Migration
{
    public function up()
    {
        Schema::create('task_recurrence_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            
            $table->foreign('task_id')->references('id')->on('tasks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_recurrence_days');
    }
}
