<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoDesignSession extends Model
{
    protected $fillable = ['child_id','parent_id','child_choices','parent_observations','agreed_interests','learning_preferences','completed_at'];
    protected function casts(): array { return ['child_choices'=>'array','parent_observations'=>'array','agreed_interests'=>'array','learning_preferences'=>'array','completed_at'=>'datetime']; }
    public function child(): BelongsTo { return $this->belongsTo(Child::class); }
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
}
