<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionCredit extends Model
{
    protected $fillable = ['child_id','enrollment_id','source_attendance_id','credit_code','reason','status','expires_at','used_course_session_id','used_at'];
    protected function casts(): array { return ['expires_at'=>'date','used_at'=>'datetime']; }
    public function child(): BelongsTo { return $this->belongsTo(Child::class); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function sourceAttendance(): BelongsTo { return $this->belongsTo(Attendance::class, 'source_attendance_id'); }
    public function usedSession(): BelongsTo { return $this->belongsTo(CourseSession::class, 'used_course_session_id'); }
}
