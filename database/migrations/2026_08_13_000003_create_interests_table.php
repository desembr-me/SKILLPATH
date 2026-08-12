<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug', 80)->unique();
            $table->string('icon', 20)->default('★');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('child_interest', function (Blueprint $table) {
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->primary(['child_profile_id', 'interest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_interest');
        Schema::dropIfExists('interests');
    }
};
