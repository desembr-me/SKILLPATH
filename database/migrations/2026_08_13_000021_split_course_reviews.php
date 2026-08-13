<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('mentor_rating')->nullable()->after('rating');
            $table->unsignedTinyInteger('platform_rating')->nullable()->after('mentor_rating');
            $table->text('mentor_review')->nullable()->after('review');
            $table->text('platform_review')->nullable()->after('mentor_review');
        });

        DB::table('course_reviews')
            ->select(['id', 'rating', 'review'])
            ->orderBy('id')
            ->chunkById(100, function ($reviews) {
                foreach ($reviews as $review) {
                    DB::table('course_reviews')->where('id', $review->id)->update([
                        'mentor_rating' => $review->rating,
                        'platform_rating' => $review->rating,
                        'mentor_review' => $review->review,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('course_reviews', function (Blueprint $table) {
            $table->dropColumn(['mentor_rating', 'platform_rating', 'mentor_review', 'platform_review']);
        });
    }
};
