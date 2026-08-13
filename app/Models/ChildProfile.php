<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildProfile extends Model
{
    protected $fillable = ['user_id','name','age','avatar'];
    public function user(){ return $this->belongsTo(User::class); }
    public function interests(){ return $this->belongsToMany(Interest::class, 'child_interest', 'child_profile_id', 'interest_id'); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }
    public function classBookings(){ return $this->hasMany(SessionBooking::class); }
    public function certificates(){ return $this->hasMany(Certificate::class); }
}
