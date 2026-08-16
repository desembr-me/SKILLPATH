<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    protected $fillable = ['parent_id','transaction_id','child_id','course_id','schedule_id','package_duration','total_sessions','status','progress','enrolled_at','completed_at','final_status'];
    protected function casts(): array { return ['enrolled_at'=>'datetime','completed_at'=>'datetime','progress'=>'integer','package_duration'=>'integer','total_sessions'=>'integer']; }

    public function getPackageInfoAttribute(): array
    {
        $duration = $this->package_duration ?: 3;
        return $this->course->getPackage($duration);
    }
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function child(): BelongsTo { return $this->belongsTo(Child::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(CourseSchedule::class, 'schedule_id'); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class, 'transaction_id'); }
    public function attendance(): HasMany { return $this->hasMany(Attendance::class); }
    public function examAttempts(): HasMany { return $this->hasMany(ExamAttempt::class); }
    public function examAttempt(): HasOne { return $this->hasOne(ExamAttempt::class)->latestOfMany(); }
    public function certificate(): HasOne { return $this->hasOne(Certificate::class); }
    public function review(): HasOne { return $this->hasOne(Review::class); }
    public function activityCompletions(): HasMany { return $this->hasMany(ActivityCompletion::class); }
    public function rescheduleRequests(): HasMany { return $this->hasMany(RescheduleRequest::class); }
}
