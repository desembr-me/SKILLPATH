<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = ['category_id','instructor_id','title','slug','subtitle','description','age_min','age_max','level','price','sessions_count','duration_minutes','location_name','address','city','cover_emoji','cover_image','accent','is_featured','status'];
    protected function casts(): array { return ['price'=>'decimal:2','is_featured'=>'boolean']; }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function schedules(): HasMany { return $this->hasMany(CourseSchedule::class); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }
    public function exams(): HasMany { return $this->hasMany(Exam::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function modules(): HasMany { return $this->hasMany(CourseModule::class)->orderBy('sequence'); }
    public function wishlistedBy(): HasMany { return $this->hasMany(Wishlist::class); }
    public function getRouteKeyName(): string { return 'slug'; }
}
