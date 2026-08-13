<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SessionCredit extends Model
{
    protected $fillable = [
        'child_profile_id', 'learning_path_id', 'source_booking_id', 'source_live_session_id',
        'used_live_session_id', 'reason', 'reason_note', 'status', 'credited_at', 'expires_at',
        'used_at', 'reactivation_count', 'last_reactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'credited_at' => 'datetime',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'last_reactivated_at' => 'datetime',
        ];
    }

    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
    public function sourceBooking(){ return $this->belongsTo(SessionBooking::class, 'source_booking_id'); }
    public function sourceLiveSession(){ return $this->belongsTo(LiveSession::class, 'source_live_session_id'); }
    public function usedLiveSession(){ return $this->belongsTo(LiveSession::class, 'used_live_session_id'); }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', 'available')
            ->where(fn (Builder $builder) => $builder
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()));
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && ! $this->isExpired();
    }
}
