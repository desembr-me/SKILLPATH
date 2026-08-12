<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Enrollment extends Model
{
    protected $fillable = ['child_profile_id','learning_path_id','order_item_id','status','enrolled_at','expires_at'];
    protected function casts(): array { return ['enrolled_at'=>'datetime','expires_at'=>'datetime']; }
    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class); }
    public function orderItem(){ return $this->belongsTo(OrderItem::class); }
}
