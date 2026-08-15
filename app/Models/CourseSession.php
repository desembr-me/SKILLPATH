<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSession extends Model
{
    protected $fillable = ['course_id','schedule_id','session_no','session_date','start_time','end_time','topic','status'];
    protected function casts(): array { return ['session_date'=>'date']; }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(CourseSchedule::class, 'schedule_id'); }
    public function attendance(): HasMany { return $this->hasMany(Attendance::class); }
}
