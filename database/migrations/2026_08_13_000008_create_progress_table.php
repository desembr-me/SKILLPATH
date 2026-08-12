<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('started');
            $table->unsignedTinyInteger('score')->nullable();
            $table->unsignedInteger('points_awarded')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['child_profile_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
