<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'final_exam_id', 'child_profile_id', 'attempt_number', 'score', 'passing_score_snapshot',
        'question_count', 'correct_answers', 'passed', 'answers', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'passed' => 'boolean',
            'answers' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function finalExam(){ return $this->belongsTo(FinalExam::class); }
    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
}
