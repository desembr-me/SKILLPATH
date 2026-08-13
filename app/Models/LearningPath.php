<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningPath extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'skill_id','instructor_id','title','slug','description','price','sale_price','is_free','course_type',
        'min_age','max_age','level','duration_minutes','icon','thumbnail_url','promo_video_url','certificate_enabled',
        'live_class_enabled','access_days','learning_outcomes','requirements','is_published','published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published'=>'boolean','is_free'=>'boolean','certificate_enabled'=>'boolean','live_class_enabled'=>'boolean',
            'price'=>'decimal:2','sale_price'=>'decimal:2','published_at'=>'datetime','deleted_at'=>'datetime',
        ];
    }

    public function getRouteKeyName(): string { return 'slug'; }
    public function skill(){ return $this->belongsTo(Skill::class); }
    public function instructor(){ return $this->belongsTo(User::class, 'instructor_id'); }
    public function interests(){ return $this->belongsToMany(Interest::class); }
    public function modules(){ return $this->hasMany(Module::class)->orderBy('order_index'); }
    public function categories(){ return $this->belongsToMany(Category::class); }
    public function reviews(){ return $this->hasMany(CourseReview::class)->where('is_approved', true); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }
    public function liveSessions(){ return $this->hasMany(LiveSession::class)->orderBy('starts_at'); }
    public function questions(){ return $this->hasMany(CourseQuestion::class)->latest(); }
    public function certificates(){ return $this->hasMany(Certificate::class); }

    public function effectivePrice(): float
    {
        if ($this->is_free) return 0;
        return (float) ($this->sale_price ?? $this->price);
    }

    public function discountPercent(): int
    {
        $price = (float) $this->price;
        $sale = (float) ($this->sale_price ?? $this->price);
        if ($price <= 0 || $sale >= $price) return 0;
        return (int) round((($price - $sale) / $price) * 100);
    }
}
