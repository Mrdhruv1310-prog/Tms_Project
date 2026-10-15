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
        Schema::table('task_verifications', function (Blueprint $table) {
            $table->string('approval_status')->default('approved')->after('status');
            $table->unsignedBigInteger('requested_by')->nullable()->after('approval_status');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_verifications', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->dropColumn(['approval_status', 'requested_by']);
        });
    }
};
