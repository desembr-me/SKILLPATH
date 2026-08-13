<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SessionBooking extends Model
{
    protected $fillable = ['live_session_id','child_profile_id','status','booked_at','booking_source','session_credit_id','absence_reason','credited_at'];
    protected function casts(): array { return ['booked_at'=>'datetime','credited_at'=>'datetime']; }
    public function liveSession(){ return $this->belongsTo(LiveSession::class); }
    public function childProfile(){ return $this->belongsTo(ChildProfile::class); }
    public function sessionCredit(){ return $this->belongsTo(SessionCredit::class); }
    public function generatedCredit(){ return $this->hasOne(SessionCredit::class, 'source_booking_id'); }
}
