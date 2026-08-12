<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CourseReview extends Model
{
    protected $fillable = ['user_id','learning_path_id','rating','review','is_approved'];
    protected function casts(): array { return ['is_approved'=>'boolean']; }
    public function user(){ return $this->belongsTo(User::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
}
