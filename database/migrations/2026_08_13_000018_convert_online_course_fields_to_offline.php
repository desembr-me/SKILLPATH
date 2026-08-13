<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('learning_paths', 'class_type')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->string('class_type', 30)->default('regular')->after('is_free');
            });
        }

        if (! Schema::hasColumn('learning_paths', 'venue_summary')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->string('venue_summary', 180)->nullable()->after('certificate_enabled');
            });
        }

        if (! Schema::hasColumn('learning_paths', 'materials_included')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->text('materials_included')->nullable()->after('venue_summary');
            });
        }

        if (Schema::hasColumn('learning_paths', 'course_type')) {
            DB::table('learning_paths')->select('id', 'course_type')->orderBy('id')->get()->each(function ($path) {
                $classType = match ($path->course_type) {
                    'live' => 'workshop',
                    default => 'regular',
                };

                DB::table('learning_paths')->where('id', $path->id)->update(['class_type' => $classType]);
            });
        }

        $legacyColumns = collect([
            'course_type',
            'promo_video_url',
            'live_class_enabled',
            'access_days',
        ])->filter(fn (string $column) => Schema::hasColumn('learning_paths', $column))->all();

        if ($legacyColumns !== []) {
            Schema::table('learning_paths', function (Blueprint $table) use ($legacyColumns) {
                $table->dropColumn($legacyColumns);
            });
        }

        Schema::dropIfExists('progress');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('learning_paths', 'course_type')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->string('course_type', 30)->default('self_paced')->after('is_free');
                $table->string('promo_video_url')->nullable()->after('thumbnail_url');
                $table->boolean('live_class_enabled')->default(false)->after('certificate_enabled');
                $table->unsignedInteger('access_days')->nullable()->after('live_class_enabled');
            });
        }
    }
};
