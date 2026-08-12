<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->after('skill_id')->constrained('users')->nullOnDelete();
            $table->decimal('price', 12, 2)->default(0)->after('description');
            $table->decimal('sale_price', 12, 2)->nullable()->after('price');
            $table->boolean('is_free')->default(false)->after('sale_price');
            $table->string('course_type', 30)->default('self_paced')->after('is_free');
            $table->string('thumbnail_url')->nullable()->after('icon');
            $table->string('promo_video_url')->nullable()->after('thumbnail_url');
            $table->boolean('certificate_enabled')->default(true)->after('promo_video_url');
            $table->boolean('live_class_enabled')->default(false)->after('certificate_enabled');
            $table->unsignedInteger('access_days')->nullable()->after('live_class_enabled');
            $table->text('learning_outcomes')->nullable()->after('access_days');
            $table->text('requirements')->nullable()->after('learning_outcomes');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn([
                'instructor_id', 'price', 'sale_price', 'is_free', 'course_type',
                'thumbnail_url', 'promo_video_url', 'certificate_enabled',
                'live_class_enabled', 'access_days', 'learning_outcomes',
                'requirements', 'published_at',
            ]);
        });
    }
};
