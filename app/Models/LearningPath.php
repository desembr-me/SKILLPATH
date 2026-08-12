<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    protected $fillable = [
        'skill_id',
        'title',
        'slug',
        'description',
        'min_age',
        'max_age',
        'level',
        'duration_minutes',
        'icon',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order_index');
    }
}
