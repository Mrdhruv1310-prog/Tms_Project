<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('task_id');
            $table->enum('type', ['due_date', 'reminder', 'overview']);
            $table->text('message')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('task_id')->references('id')->on('tasks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
