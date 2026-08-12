<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InstructorProfile extends Model
{
    protected $fillable = ['user_id','headline','bio','expertise','years_experience','education','photo_url','is_verified','rating','students_count'];
    protected function casts(): array { return ['is_verified'=>'boolean','rating'=>'decimal:2']; }
    public function user(){ return $this->belongsTo(User::class); }
}
