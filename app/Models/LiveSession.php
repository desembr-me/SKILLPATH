<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LiveSession extends Model
{
    protected $fillable = ['learning_path_id','instructor_id','title','description','starts_at','ends_at','meeting_url','capacity','status','recording_url'];
    protected function casts(): array { return ['starts_at'=>'datetime','ends_at'=>'datetime']; }
    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
    public function instructor(){ return $this->belongsTo(User::class, 'instructor_id'); }
    public function bookings(){ return $this->hasMany(SessionBooking::class); }
}
