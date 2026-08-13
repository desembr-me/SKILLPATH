<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionBooking extends Model
{
    protected $fillable = [
        'class_session_id', 'child_profile_id', 'status', 'booked_at', 'checked_in_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['booked_at' => 'datetime', 'checked_in_at' => 'datetime'];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'booked' => 'Terdaftar',
            'attended' => 'Hadir',
            'absent' => 'Tidak hadir',
            'cancelled' => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function classSession(){ return $this->belongsTo(ClassSession::class); }
    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
}
