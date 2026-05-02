<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskUpdatesTable extends Migration
{
    public function up()
    {
        Schema::create('task_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_updates');
    }
}
