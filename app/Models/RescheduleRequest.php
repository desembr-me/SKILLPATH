<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RescheduleRequest extends Model
{
    protected $fillable = [
        'enrollment_id',
        'parent_id',
        'mentor_id',
        'current_schedule_id',
        'requested_schedule_id',
        'reason',
        'status',
        'mentor_note',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function currentSchedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'current_schedule_id');
    }

    public function requestedSchedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'requested_schedule_id');
    }
}
