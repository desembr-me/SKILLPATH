<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseReview extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id','learning_path_id','rating','review','mentor_rating','platform_rating','mentor_review','platform_review','is_approved'];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(){ return $this->belongsTo(User::class)->withTrashed(); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class)->withTrashed(); }

    public function problemSource(): string
    {
        $mentor = (int) ($this->mentor_rating ?? $this->rating);
        $platform = (int) ($this->platform_rating ?? $this->rating);
        if ($mentor <= 2 && $platform <= 2) return 'Mentor & Platform';
        if ($mentor <= 2) return 'Mentor';
        if ($platform <= 2) return 'Platform';
        return 'Tidak ada indikasi masalah';
    }
}
