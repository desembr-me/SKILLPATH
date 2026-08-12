<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->string('slug', 140)->unique();
            $table->text('description');
            $table->unsignedTinyInteger('min_age');
            $table->unsignedTinyInteger('max_age');
            $table->string('level', 30)->default('Pemula');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->string('icon', 20)->default('✦');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('interest_learning_path', function (Blueprint $table) {
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->primary(['interest_id', 'learning_path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_learning_path');
        Schema::dropIfExists('learning_paths');
    }
};
