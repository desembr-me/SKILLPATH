<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasColumn('users', 'deleted_at')) {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    if (!Schema::hasColumn('learning_paths', 'deleted_at')) {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    if (!Schema::hasColumn('categories', 'deleted_at')) {
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    if (!Schema::hasColumn('course_reviews', 'deleted_at')) {
        Schema::table('course_reviews', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
    public function down(): void
    {
        Schema::table('course_reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
