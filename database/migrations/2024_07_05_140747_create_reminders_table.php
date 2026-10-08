<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('reminder_time');
            $table->enum('reminder_unit', ['minutes', 'hours', 'days']);
            $table->integer('reminder_value');

            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
}
