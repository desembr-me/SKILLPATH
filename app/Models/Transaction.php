<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Transaction extends Model
{
    protected $fillable = [
        'parent_id',
        'enrollment_id',
        'invoice_code',
        'subtotal',
        'platform_fee',
        'total',
        'payment_method',
        'status',
        'paid_at',
        'metadata'
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array'
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Primary / legacy enrollment association.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    /**
     * All enrollments associated with this multi-course transaction.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'transaction_id');
    }

    /**
     * Helper to retrieve all associated enrollments seamlessly.
     */
    public function getAllEnrollmentsAttribute(): Collection
    {
        if ($this->relationLoaded('enrollments') && $this->enrollments->isNotEmpty()) {
            return $this->enrollments;
        }

        $direct = $this->enrollments()->with(['course.category', 'course.instructor', 'child', 'schedule'])->get();
        if ($direct->isNotEmpty()) {
            return $direct;
        }

        if ($this->enrollment) {
            return collect([$this->enrollment]);
        }

        return collect();
    }

    /**
     * Check if this transaction is a bundled multi-course checkout.
     */
    public function getIsMultiCourseAttribute(): bool
    {
        return $this->all_enrollments->count() > 1;
    }
}
