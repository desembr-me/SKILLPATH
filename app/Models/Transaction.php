<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['parent_id','enrollment_id','invoice_code','subtotal','platform_fee','total','payment_method','status','paid_at','metadata'];
    protected function casts(): array { return ['subtotal'=>'decimal:2','platform_fee'=>'decimal:2','total'=>'decimal:2','paid_at'=>'datetime','metadata'=>'array']; }
    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
}
