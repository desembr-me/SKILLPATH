<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_PARENT = 'parent';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = ['name','email','phone','password','role','avatar'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed']; }

    public function children(): HasMany { return $this->hasMany(Child::class, 'parent_id'); }
    public function courses(): HasMany { return $this->hasMany(Course::class, 'instructor_id'); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class, 'parent_id'); }
    public function reviews(): HasMany { return $this->hasMany(Review::class, 'parent_id'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class, 'parent_id'); }
    public function wishlists(): HasMany { return $this->hasMany(Wishlist::class, 'parent_id'); }
    public function cartItems(): HasMany { return $this->hasMany(CartItem::class, 'parent_id'); }

    public function isParent(): bool { return $this->role === self::ROLE_PARENT; }
    public function isMentor(): bool { return $this->role === self::ROLE_MENTOR; }
    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }
}
