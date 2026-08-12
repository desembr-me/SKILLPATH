<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug', 80)->unique();
            $table->string('icon', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('category_learning_path', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('learning_path_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary(['category_id', 'learning_path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_learning_path');
        Schema::dropIfExists('categories');
    }
};
