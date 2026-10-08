<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->enum('type', ['due_date', 'reminder', 'overview']);
            $table->text('message')->nullable();
            $table->timestamp('sent_at')->useCurrent();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}
