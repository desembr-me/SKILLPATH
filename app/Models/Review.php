<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['parent_id','enrollment_id','course_id','instructor_id','mentor_rating','mentor_review','platform_rating','platform_review','is_published'];
    protected function casts(): array { return ['mentor_rating'=>'integer','platform_rating'=>'integer','is_published'=>'boolean']; }
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
}
