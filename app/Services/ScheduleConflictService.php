<?php
namespace App\Services;
use App\Models\Child; use App\Models\CourseSchedule; use App\Models\Enrollment;
class ScheduleConflictService {
 public function conflicts(Child $child, CourseSchedule $candidate, ?int $excludeEnrollmentId = null): array {
  return Enrollment::query()->with(['course','schedule'])->where('child_id',$child->id)->whereIn('status',['pending_payment','active'])
   ->when($excludeEnrollmentId, fn($q)=>$q->whereKeyNot($excludeEnrollmentId))
   ->whereHas('schedule',fn($q)=>$q->where('day_of_week',$candidate->day_of_week)->where('start_time','<',$candidate->end_time)->where('end_time','>',$candidate->start_time))
   ->get()->map(fn($e)=>['course'=>$e->course->title,'schedule'=>$e->schedule->start_time.' - '.$e->schedule->end_time])->all();
 }
 public function alternatives(CourseSchedule $candidate) { return CourseSchedule::with('course')->where('course_id',$candidate->course_id)->where('status','open')->whereKeyNot($candidate->id)->orderBy('day_of_week')->orderBy('start_time')->limit(3)->get(); }
}
