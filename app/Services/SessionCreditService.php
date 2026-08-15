<?php
namespace App\Services;
use App\Models\Attendance; use App\Models\SessionCredit; use Illuminate\Support\Str; use Illuminate\Validation\ValidationException;
class SessionCreditService {
 public function createFromAttendance(Attendance $attendance): SessionCredit {
  if(!$attendance->credit_eligible) throw ValidationException::withMessages(['credit'=>'Sesi ini tidak memenuhi syarat kredit.']);
  return SessionCredit::firstOrCreate(['source_attendance_id'=>$attendance->id],['child_id'=>$attendance->enrollment->child_id,'enrollment_id'=>$attendance->enrollment_id,'credit_code'=>'CR-'.strtoupper(Str::random(8)),'reason'=>$attendance->absence_reason ?: 'Sesi tidak dapat diikuti','status'=>'available','expires_at'=>now()->addDays(45)]);
 }
}
