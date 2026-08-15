<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $fillable = ['course_id','title','description','sequence'];
    protected function casts(): array { return ['sequence'=>'integer']; }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function activities(): HasMany { return $this->hasMany(ModuleActivity::class); }
}
