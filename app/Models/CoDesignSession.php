<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoDesignSession extends Model
{
    protected $fillable = [
        'child_profile_id',
        'selected_interest_ids',
        'favorite_interest_id',
        'learning_need',
        'child_voice',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'selected_interest_ids' => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    public function childProfile()
    {
        return $this->belongsTo(ChildProfile::class);
    }

    public function favoriteInterest()
    {
        return $this->belongsTo(Interest::class, 'favorite_interest_id');
    }
}
