<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number', 60)->unique();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
            $table->unique(['child_profile_id', 'learning_path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
