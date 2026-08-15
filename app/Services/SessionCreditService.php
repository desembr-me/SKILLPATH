<?php
namespace App\Services;
use App\Models\Attendance; use App\Models\CourseSession; use App\Models\SessionCredit; use Illuminate\Support\Str; use Illuminate\Validation\ValidationException;
class SessionCreditService {
 public function createFromAttendance(Attendance $attendance): SessionCredit {
  if(!$attendance->credit_eligible) throw ValidationException::withMessages(['credit'=>'Sesi ini tidak memenuhi syarat kredit.']);
  return SessionCredit::firstOrCreate(['source_attendance_id'=>$attendance->id],['child_id'=>$attendance->enrollment->child_id,'enrollment_id'=>$attendance->enrollment_id,'credit_code'=>'CR-'.strtoupper(Str::random(8)),'reason'=>$attendance->absence_reason ?: 'Sesi tidak dapat diikuti','status'=>'available','expires_at'=>now()->addDays(45)]);
 }
 public function redeem(SessionCredit $credit, CourseSession $target): SessionCredit {
  if($credit->status!=='available' || $credit->expires_at->isPast()) throw ValidationException::withMessages(['credit'=>'Kredit tidak tersedia atau sudah kedaluwarsa.']);
  $credit->update(['status'=>'used','used_course_session_id'=>$target->id,'used_at'=>now()]); return $credit;
 }
}
