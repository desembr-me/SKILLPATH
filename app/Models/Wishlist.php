<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Wishlist extends Model
{
    protected $fillable = ['user_id','learning_path_id'];
    public function user(){ return $this->belongsTo(User::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
}
