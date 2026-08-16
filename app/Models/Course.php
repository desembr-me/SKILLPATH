<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = ['category_id','instructor_id','title','slug','subtitle','description','age_min','age_max','level','price','sessions_count','duration_minutes','location_name','address','city','cover_emoji','cover_image','accent','is_featured','status'];
    protected function casts(): array { return ['price'=>'decimal:2','is_featured'=>'boolean']; }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function schedules(): HasMany { return $this->hasMany(CourseSchedule::class); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }
    public function exams(): HasMany { return $this->hasMany(Exam::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function modules(): HasMany { return $this->hasMany(CourseModule::class)->orderBy('sequence'); }
    public function wishlistedBy(): HasMany { return $this->hasMany(Wishlist::class); }
    public function getRouteKeyName(): string { return 'slug'; }

    public function getPackagesAttribute(): array
    {
        $basePrice = (float) $this->price;
        $baseSessions = (int) ($this->sessions_count ?: 6);

        return [
            3 => [
                'duration_months' => 3,
                'key' => '3_months',
                'name' => '3 Bulan',
                'title' => 'Paket 3 Bulan',
                'badge' => 'Eksplorasi Dasar',
                'badge_type' => 'normal',
                'sessions' => $baseSessions,
                'original_price' => $basePrice,
                'price' => $basePrice,
                'discount_percent' => 0,
                'savings' => 0,
                'price_per_month' => round($basePrice / 3),
                'features' => [
                    $baseSessions . ' Sesi Belajar Tatap Muka',
                    'Kelompok Kecil (Maks. 6 Anak)',
                    'Modul & Perlengkapan Standar',
                    'Sertifikat Level Dasar'
                ],
                'highlight' => false
            ],
            6 => [
                'duration_months' => 6,
                'key' => '6_months',
                'name' => '6 Bulan',
                'title' => 'Paket 6 Bulan',
                'badge' => 'Paling Populer',
                'badge_type' => 'popular',
                'sessions' => $baseSessions * 2,
                'original_price' => $basePrice * 2,
                'price' => round(($basePrice * 2) * 0.85), // Diskon 15%
                'discount_percent' => 15,
                'savings' => round(($basePrice * 2) * 0.15),
                'price_per_month' => round((($basePrice * 2) * 0.85) / 6),
                'features' => [
                    ($baseSessions * 2) . ' Sesi Belajar Intensif',
                    'Bimbingan Proyek Karya Mandiri',
                    'Showcase & Evaluasi Komprehensif',
                    'Sertifikat Menengah + Portofolio',
                    'Prioritas Reschedule Jadwal'
                ],
                'highlight' => true
            ],
            12 => [
                'duration_months' => 12,
                'key' => '1_year',
                'name' => '1 Tahun',
                'title' => 'Paket 1 Tahun',
                'badge' => 'Hemat 25% • Nilai Terbaik',
                'badge_type' => 'best_value',
                'sessions' => $baseSessions * 4,
                'original_price' => $basePrice * 4,
                'price' => round(($basePrice * 4) * 0.75), // Diskon 25%
                'discount_percent' => 25,
                'savings' => round(($basePrice * 4) * 0.25),
                'price_per_month' => round((($basePrice * 4) * 0.75) / 12),
                'features' => [
                    ($baseSessions * 4) . ' Sesi Lengkap Hingga Mahir',
                    'Akses Seluruh Modul Tingkat Mahir',
                    'Ujian Sertifikasi Resmi & Medali',
                    'Laporan Bakat & Portofolio Lengkap',
                    'Bebas Reschedule Jadwal Sepuasnya'
                ],
                'highlight' => false
            ]
        ];
    }

    public function getPackage(int $months): array
    {
        $packages = $this->packages;
        return $packages[$months] ?? $packages[3];
    }
}
