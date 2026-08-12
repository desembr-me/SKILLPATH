<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at'=>'datetime','password'=>'hashed'];
    }

    public function childProfile(){ return $this->hasOne(ChildProfile::class); }
    public function instructorProfile(){ return $this->hasOne(InstructorProfile::class); }
    public function coursesTaught(){ return $this->hasMany(LearningPath::class, 'instructor_id'); }
    public function orders(){ return $this->hasMany(Order::class); }
    public function wishlists(){ return $this->hasMany(Wishlist::class); }
    public function reviews(){ return $this->hasMany(CourseReview::class); }
    public function questions(){ return $this->hasMany(CourseQuestion::class); }
}
