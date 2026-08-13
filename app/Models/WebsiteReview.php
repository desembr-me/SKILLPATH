<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteReview extends Model
{
    protected $table = 'website_reviews';

    protected $fillable = ['user_id', 'rating', 'review', 'is_published'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
