<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('meeting_url')->nullable();
            $table->unsignedInteger('capacity')->default(30);
            $table->string('status', 20)->default('scheduled');
            $table->string('recording_url')->nullable();
            $table->timestamps();
        });

        Schema::create('session_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('booked');
            $table->timestamp('booked_at')->nullable();
            $table->timestamps();
            $table->unique(['live_session_id', 'child_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_bookings');
        Schema::dropIfExists('live_sessions');
    }
};
