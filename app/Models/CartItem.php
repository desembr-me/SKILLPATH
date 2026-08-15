<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['parent_id','child_id','schedule_id'];
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function child(): BelongsTo { return $this->belongsTo(Child::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(CourseSchedule::class, 'schedule_id'); }
}
