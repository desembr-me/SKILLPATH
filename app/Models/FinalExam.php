<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExam extends Model
{
    protected $fillable = ['learning_path_id', 'title', 'passing_score', 'max_attempts', 'questions', 'is_active'];

    protected function casts(): array
    {
        return ['questions' => 'array', 'is_active' => 'boolean'];
    }

    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
    public function attempts(){ return $this->hasMany(ExamAttempt::class); }
}
