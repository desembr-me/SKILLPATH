<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_credits', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('credited_at');
            $table->unsignedSmallInteger('reactivation_count')->default(0)->after('used_at');
            $table->timestamp('last_reactivated_at')->nullable()->after('reactivation_count');
            $table->index(['child_profile_id', 'learning_path_id', 'status'], 'session_credit_lookup_index');
        });

        DB::table('session_credits')
            ->orderBy('id')
            ->chunkById(100, function ($credits) {
                foreach ($credits as $credit) {
                    $expiresAt = DB::table('enrollments')
                        ->where('child_profile_id', $credit->child_profile_id)
                        ->where('learning_path_id', $credit->learning_path_id)
                        ->value('expires_at');

                    if ($expiresAt) {
                        DB::table('session_credits')
                            ->where('id', $credit->id)
                            ->update(['expires_at' => $expiresAt]);
                    }
                }
            });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unsignedTinyInteger('passing_score_snapshot')->nullable()->after('score');
            $table->unsignedSmallInteger('question_count')->nullable()->after('passing_score_snapshot');
            $table->unsignedSmallInteger('correct_answers')->nullable()->after('question_count');
        });

        Schema::create('co_design_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->json('selected_interest_ids');
            $table->foreignId('favorite_interest_id')->nullable()->constrained('interests')->nullOnDelete();
            $table->string('learning_need', 50);
            $table->text('child_voice');
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['child_profile_id', 'recorded_at']);
        });

        // Backfill satu snapshot untuk profil yang sudah menyelesaikan co-design
        // pada versi fitur sebelumnya.
        DB::table('child_profiles')
            ->whereNotNull('co_design_completed_at')
            ->orderBy('id')
            ->chunkById(100, function ($children) {
                foreach ($children as $child) {
                    $interestIds = DB::table('child_interest')
                        ->where('child_profile_id', $child->id)
                        ->orderBy('interest_id')
                        ->pluck('interest_id')
                        ->map(fn ($id) => (int) $id)
                        ->values()
                        ->all();

                    DB::table('co_design_sessions')->insert([
                        'child_profile_id' => $child->id,
                        'selected_interest_ids' => json_encode($interestIds),
                        'favorite_interest_id' => $child->favorite_interest_id,
                        'learning_need' => $child->learning_need ?: 'confidence',
                        'child_voice' => $child->child_voice ?: 'Preferensi anak belum dicatat pada versi sebelumnya.',
                        'recorded_at' => $child->co_design_completed_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('co_design_sessions');

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn(['passing_score_snapshot', 'question_count', 'correct_answers']);
        });

        Schema::table('session_credits', function (Blueprint $table) {
            $table->dropIndex('session_credit_lookup_index');
            $table->dropColumn(['expires_at', 'reactivation_count', 'last_reactivated_at']);
        });
    }
};
