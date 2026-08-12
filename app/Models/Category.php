<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class);
    }
}
