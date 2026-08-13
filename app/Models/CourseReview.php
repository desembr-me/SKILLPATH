<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseReview extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id','learning_path_id','rating','review','is_approved'];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(){ return $this->belongsTo(User::class)->withTrashed(); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class)->withTrashed(); }
}
