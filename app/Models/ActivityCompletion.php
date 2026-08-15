<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityCompletion extends Model
{
    protected $fillable = ['enrollment_id','module_activity_id','completed_at'];
    protected function casts(): array { return ['completed_at'=>'datetime']; }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function activity(): BelongsTo { return $this->belongsTo(ModuleActivity::class, 'module_activity_id'); }
}
