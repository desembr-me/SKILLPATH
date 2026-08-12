<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
    ];

    public function children()
    {
        return $this->belongsToMany(ChildProfile::class);
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class);
    }
}
