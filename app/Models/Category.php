<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name','slug','emoji','description','accent'];
    public function courses(): HasMany { return $this->hasMany(Course::class); }
    public function mentors(): HasMany { return $this->hasMany(User::class, 'category_id')->where('role', 'mentor'); }
}
