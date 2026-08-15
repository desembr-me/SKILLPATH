<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = ['parent_id','course_id'];
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
}
