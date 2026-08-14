<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public const CORE_CATEGORIES = [
        'arts' => [
            'name' => 'Arts',
            'icon' => '✎',
            'description' => 'Menggambar, melukis, craft, storytelling, dan berbagai aktivitas kreatif anak.',
        ],
        'music' => [
            'name' => 'Music',
            'icon' => '♫',
            'description' => 'Eksplorasi ritme, vokal, instrumen, dan ekspresi musik melalui praktik langsung.',
        ],
        'self-improvement' => [
            'name' => 'Self Improvement',
            'icon' => '✧',
            'description' => 'Percaya diri, komunikasi, kemandirian, emosi, kebiasaan positif, dan social skill.',
        ],
        'languages' => [
            'name' => 'Languages',
            'icon' => 'Aa',
            'description' => 'Belajar bahasa melalui percakapan, permainan, role play, dan aktivitas komunikatif.',
        ],
        'sports' => [
            'name' => 'Sports',
            'icon' => '●',
            'description' => 'Aktivitas fisik untuk koordinasi, kebugaran, disiplin, teamwork, dan sportivitas.',
        ],
        'technology' => [
            'name' => 'Technology',
            'icon' => '</>',
            'description' => 'Coding, robotik, logika digital, desain, dan teknologi kreatif melalui proyek praktik.',
        ],
    ];

    protected $fillable = ['name', 'slug', 'icon', 'description'];

    protected function casts(): array
    {
        return ['deleted_at' => 'datetime'];
    }

    public static function coreSlugs(): array
    {
        return array_keys(self::CORE_CATEGORIES);
    }

    public static function orderedCore($categories)
    {
        $order = array_flip(self::coreSlugs());

        return $categories->sortBy(fn ($category) => $order[$category->slug] ?? 999)->values();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class);
    }
}
