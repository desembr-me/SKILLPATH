<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    protected $fillable = ['user_id','order_number','subtotal','discount','total','payment_method','payment_status','status','paid_at'];
    protected function casts(): array { return ['paid_at'=>'datetime','subtotal'=>'decimal:2','discount'=>'decimal:2','total'=>'decimal:2']; }
    public function user(){ return $this->belongsTo(User::class); }
    public function items(){ return $this->hasMany(OrderItem::class); }
}
