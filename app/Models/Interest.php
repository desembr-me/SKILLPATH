<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Interest extends Model
{
    protected $fillable = ['name','slug','icon','description'];
    public function children(){ return $this->belongsToMany(ChildProfile::class, 'child_interest', 'interest_id', 'child_profile_id'); }
    public function learningPaths(){ return $this->belongsToMany(LearningPath::class); }
}
