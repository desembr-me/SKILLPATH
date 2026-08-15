<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSchedule extends Model
{
    protected $fillable = ['course_id','instructor_id','day_of_week','start_time','end_time','start_date','end_date','capacity','room','status'];
    protected function casts(): array { return ['start_date'=>'date','end_date'=>'date']; }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class, 'schedule_id'); }
    public function sessions(): HasMany { return $this->hasMany(CourseSession::class, 'schedule_id'); }
}
