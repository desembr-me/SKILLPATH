<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_PARENT = 'parent';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = ['name','email','phone','password','role','avatar','headline','bio','category_id'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed']; }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function children(): HasMany { return $this->hasMany(Child::class, 'parent_id'); }
    public function courses(): HasMany { return $this->hasMany(Course::class, 'instructor_id'); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class, 'parent_id'); }
    public function reviews(): HasMany { return $this->hasMany(Review::class, 'parent_id'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class, 'parent_id'); }
    public function wishlists(): HasMany { return $this->hasMany(Wishlist::class, 'parent_id'); }
    public function cartItems(): HasMany { return $this->hasMany(CartItem::class, 'parent_id'); }

    public function mentorRescheduleRequests(): HasMany { return $this->hasMany(RescheduleRequest::class, 'mentor_id'); }
    public function parentRescheduleRequests(): HasMany { return $this->hasMany(RescheduleRequest::class, 'parent_id'); }
    public function unreadRescheduleRequestsCount(): int { return $this->mentorRescheduleRequests()->where('is_read', false)->count(); }

    public function isParent(): bool { return $this->role === self::ROLE_PARENT; }
    public function isMentor(): bool { return $this->role === self::ROLE_MENTOR; }
    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }
        if (str_starts_with($this->avatar, 'avatars/') || str_starts_with($this->avatar, 'photos/')) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }
        if (file_exists(public_path($this->avatar))) {
            return asset($this->avatar);
        }
        return \Illuminate\Support\Facades\Storage::url($this->avatar);
    }

    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->name ?? 'U', 0, 1));
    }
}
