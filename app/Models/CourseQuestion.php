<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CourseQuestion extends Model
{
    protected $fillable = ['user_id','child_profile_id','learning_path_id','question','is_resolved'];
    protected function casts(): array { return ['is_resolved'=>'boolean']; }
    public function user(){ return $this->belongsTo(User::class)->withTrashed(); }
    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class)->withTrashed(); }
    public function answers(){ return $this->hasMany(CourseAnswer::class); }
}
