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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('label_id')->nullable()->after('title');
            $table->date('recurrence_end_date')->nullable()->after('recurrence');
            $table->string('reminderTime')->nullable()->after('recurrence_end_date');
            $table->string('reminderUnit')->nullable()->after('reminderTime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
