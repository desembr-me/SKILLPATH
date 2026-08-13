<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildProfile extends Model
{
    protected $fillable = ['user_id','name','age','avatar','favorite_interest_id','learning_need','child_voice','co_design_completed_at'];
    public function user(){ return $this->belongsTo(User::class); }
    public function interests(){ return $this->belongsToMany(Interest::class, 'child_interest', 'child_profile_id', 'interest_id'); }
    public function progress(){ return $this->hasMany(Progress::class); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }
    public function liveBookings(){ return $this->hasMany(SessionBooking::class); }
    public function certificates(){ return $this->hasMany(Certificate::class); }
    public function favoriteInterest(){ return $this->belongsTo(Interest::class, 'favorite_interest_id'); }
    public function sessionCredits(){ return $this->hasMany(SessionCredit::class); }
    public function examAttempts(){ return $this->hasMany(ExamAttempt::class); }
    public function coDesignSessions(){ return $this->hasMany(CoDesignSession::class)->latest('recorded_at'); }

    protected function casts(): array { return ['co_design_completed_at' => 'datetime']; }
}
