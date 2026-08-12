<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Certificate extends Model
{
    protected $fillable = ['child_profile_id','learning_path_id','certificate_number','final_score','issued_at'];
    protected function casts(): array { return ['issued_at'=>'datetime','final_score'=>'decimal:2']; }
    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
}
