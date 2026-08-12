<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'learning_path_id',
        'title',
        'slug',
        'summary',
        'order_index',
        'estimated_minutes',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function learningPath()
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('order_index');
    }
}
