<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningPath extends Model
{
    protected $fillable = ['child_id','title','rationale','status','generated_at'];
    protected function casts(): array { return ['generated_at'=>'datetime']; }
    public function child(): BelongsTo { return $this->belongsTo(Child::class); }
    public function items(): HasMany { return $this->hasMany(LearningPathItem::class)->orderBy('sequence'); }
}
