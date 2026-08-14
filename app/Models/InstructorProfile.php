<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InstructorProfile extends Model
{
    protected $fillable = [
        'user_id', 'headline', 'bio', 'expertise', 'years_experience', 'education',
        'photo_url', 'is_verified', 'rating', 'students_count',
    ];

    protected function casts(): array
    {
        return ['is_verified' => 'boolean', 'rating' => 'decimal:2'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photoSrc(): ?string
    {
        if (! $this->photo_url) {
            return null;
        }

        if (Str::startsWith($this->photo_url, ['http://', 'https://'])) {
            return $this->photo_url;
        }

        return asset(ltrim($this->photo_url, '/'));
    }
}
