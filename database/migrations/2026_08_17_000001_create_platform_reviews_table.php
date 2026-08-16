<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create platform_reviews table (1 review per parent)
        if (!Schema::hasTable('platform_reviews')) {
            Schema::create('platform_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('review')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // 2. Make platform_rating and platform_review nullable on reviews table if exists
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unsignedTinyInteger('platform_rating')->nullable()->change();
                $table->text('platform_review')->nullable()->change();
            });

            // 3. Migrate existing platform ratings into platform_reviews
            $existing = DB::table('reviews')
                ->whereNotNull('platform_rating')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($existing as $rev) {
                if (!DB::table('platform_reviews')->where('parent_id', $rev->parent_id)->exists()) {
                    DB::table('platform_reviews')->insert([
                        'parent_id' => $rev->parent_id,
                        'rating' => $rev->platform_rating,
                        'review' => $rev->platform_review,
                        'is_published' => $rev->is_published ?? true,
                        'created_at' => $rev->created_at ?? now(),
                        'updated_at' => $rev->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_reviews');
    }
};
