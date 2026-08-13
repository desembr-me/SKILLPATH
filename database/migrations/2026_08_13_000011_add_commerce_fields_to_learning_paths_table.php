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
            $table->string('class_type', 30)->default('regular')->after('is_free');
            $table->string('thumbnail_url')->nullable()->after('icon');
            $table->boolean('certificate_enabled')->default(true)->after('thumbnail_url');
            $table->string('venue_summary', 180)->nullable()->after('certificate_enabled');
            $table->text('materials_included')->nullable()->after('venue_summary');
            $table->text('learning_outcomes')->nullable()->after('materials_included');
            $table->text('requirements')->nullable()->after('learning_outcomes');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn([
                'instructor_id', 'price', 'sale_price', 'is_free', 'class_type',
                'thumbnail_url', 'certificate_enabled', 'venue_summary',
                'materials_included', 'learning_outcomes', 'requirements', 'published_at',
            ]);
        });
    }
};
