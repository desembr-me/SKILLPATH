<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progress';

    protected $fillable = [
        'child_profile_id',
        'activity_id',
        'status',
        'score',
        'points_awarded',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function childProfile()
    {
        return $this->belongsTo(ChildProfile::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
