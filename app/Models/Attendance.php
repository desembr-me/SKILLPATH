<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['enrollment_id','course_session_id','status','absence_reason','credit_eligible','mentor_note'];
    protected function casts(): array { return ['credit_eligible'=>'boolean']; }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function courseSession(): BelongsTo { return $this->belongsTo(CourseSession::class); }
    public function credit(): HasOne { return $this->hasOne(SessionCredit::class, 'source_attendance_id'); }
}
