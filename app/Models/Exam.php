<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = ['course_id','title','description','passing_score','max_attempts','is_active'];
    protected function casts(): array { return ['passing_score'=>'integer','max_attempts'=>'integer','is_active'=>'boolean']; }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function attempts(): HasMany { return $this->hasMany(ExamAttempt::class); }
}
