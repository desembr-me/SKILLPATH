<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_booking_id')->nullable()->unique()->constrained('session_bookings')->nullOnDelete();
            $table->foreignId('source_live_session_id')->nullable()->constrained('live_sessions')->nullOnDelete();
            $table->foreignId('used_live_session_id')->nullable()->constrained('live_sessions')->nullOnDelete();
            $table->string('reason', 30);
            $table->text('reason_note')->nullable();
            $table->string('status', 20)->default('available')->index();
            $table->timestamp('credited_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::table('session_bookings', function (Blueprint $table) {
            $table->string('booking_source', 20)->default('direct')->after('status');
            $table->foreignId('session_credit_id')->nullable()->after('booking_source')->constrained('session_credits')->nullOnDelete();
            $table->string('absence_reason', 30)->nullable()->after('session_credit_id');
            $table->timestamp('credited_at')->nullable()->after('absence_reason');
            $table->index(['child_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('session_bookings', function (Blueprint $table) {
            $table->dropForeign(['session_credit_id']);
            $table->dropIndex(['child_profile_id', 'status']);
            $table->dropColumn(['booking_source', 'session_credit_id', 'absence_reason', 'credited_at']);
        });

        Schema::dropIfExists('session_credits');
    }
};
