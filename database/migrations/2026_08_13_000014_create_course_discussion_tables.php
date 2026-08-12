<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('child_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
        });

        Schema::create('course_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('answer');
            $table->boolean('is_instructor')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_answers');
        Schema::dropIfExists('course_questions');
    }
};
