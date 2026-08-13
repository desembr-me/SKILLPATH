<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CourseAnswer extends Model
{
    protected $fillable = ['course_question_id','user_id','answer','is_instructor'];
    protected function casts(): array { return ['is_instructor'=>'boolean']; }
    public function question(){ return $this->belongsTo(CourseQuestion::class, 'course_question_id'); }
    public function user(){ return $this->belongsTo(User::class)->withTrashed(); }
}
