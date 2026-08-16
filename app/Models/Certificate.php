<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = ['enrollment_id','exam_attempt_id','certificate_no','issued_at','file_path'];
    protected function casts(): array { return ['issued_at'=>'datetime']; }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function examAttempt(): BelongsTo { return $this->belongsTo(ExamAttempt::class); }
    public function getCertificateNumberAttribute(): ?string { return $this->certificate_no; }
}
