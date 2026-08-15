<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    protected $fillable = ['parent_id','name','birth_date','nickname','avatar','interests','learning_preferences','notes'];
    protected function casts(): array { return ['birth_date'=>'date','interests'=>'array','learning_preferences'=>'array']; }
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }
    public function credits(): HasMany { return $this->hasMany(SessionCredit::class); }
    public function coDesignSessions(): HasMany { return $this->hasMany(CoDesignSession::class); }
    public function learningPaths(): HasMany { return $this->hasMany(LearningPath::class); }
    public function getAgeAttribute(): int { return $this->birth_date?->age ?? 0; }
}
