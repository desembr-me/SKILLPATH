<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['parent_id','child_id','schedule_id','package_duration'];
    protected function casts(): array { return ['package_duration' => 'integer']; }

    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function child(): BelongsTo { return $this->belongsTo(Child::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(CourseSchedule::class, 'schedule_id'); }

    public function getPackageInfoAttribute(): array
    {
        $duration = $this->package_duration ?: 3;
        return $this->schedule->course->getPackage($duration);
    }

    public function getCalculatedPriceAttribute(): float
    {
        return (float) $this->package_info['price'];
    }
}
