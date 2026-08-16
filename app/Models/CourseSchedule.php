<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSchedule extends Model
{
    protected $fillable = ['course_id','instructor_id','day_of_week','start_time','end_time','start_date','end_date','capacity','room','status'];
    protected function casts(): array { return ['start_date'=>'date','end_date'=>'date']; }
    public const DAYS = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public function getDayNameAttribute(): string
    {
        if (is_numeric($this->day_of_week)) {
            return self::DAYS[(int) $this->day_of_week] ?? ('Hari ' . $this->day_of_week);
        }
        return (string) $this->day_of_week;
    }

    public function getFormattedTimeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5) . ' WIB';
    }

    public function getFormattedScheduleAttribute(): string
    {
        $room = $this->room ? ' (' . $this->room . ')' : '';
        return 'Hari ' . $this->day_name . ' • ' . $this->formatted_time . $room;
    }

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class, 'schedule_id'); }
    public function sessions(): HasMany { return $this->hasMany(CourseSession::class, 'schedule_id'); }
}
