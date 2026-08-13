<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model
{
    protected $fillable = ['order_id','learning_path_id','title_snapshot','price','discount','final_price'];
    protected function casts(): array { return ['price'=>'decimal:2','discount'=>'decimal:2','final_price'=>'decimal:2']; }
    public function order(){ return $this->belongsTo(Order::class); }
    public function learningPath(){ return $this->belongsTo(LearningPath::class)->withTrashed(); }
    public function enrollment(){ return $this->hasOne(Enrollment::class); }
}
