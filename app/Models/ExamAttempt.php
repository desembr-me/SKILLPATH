<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamAttempt extends Model
{
    protected $fillable = ['exam_id','enrollment_id','attempt_no','score','status','mentor_feedback','taken_at'];
    protected function casts(): array { return ['score'=>'integer','attempt_no'=>'integer','taken_at'=>'datetime']; }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function certificate(): HasOne { return $this->hasOne(Certificate::class); }
}
