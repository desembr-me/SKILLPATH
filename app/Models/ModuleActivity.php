<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleActivity extends Model
{
    protected $fillable = ['course_module_id','title','type','sequence'];
    protected function casts(): array { return ['sequence'=>'integer']; }
    public function module(): BelongsTo { return $this->belongsTo(CourseModule::class, 'course_module_id'); }
    public function completions(): HasMany { return $this->hasMany(ActivityCompletion::class); }
}
