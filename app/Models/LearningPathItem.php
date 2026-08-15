<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningPathItem extends Model
{
    protected $fillable = ['learning_path_id','course_id','sequence','reason','status','match_score'];
    public function learningPath(): BelongsTo { return $this->belongsTo(LearningPath::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
}
