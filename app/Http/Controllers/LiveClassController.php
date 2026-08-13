<?php
namespace App\Http\Controllers;
use App\Models\Enrollment;
use App\Models\LiveSession;
use App\Models\SessionBooking;
use Illuminate\Http\Request;
class LiveClassController extends Controller
{
    public function index(Request $request){
        $child=$request->user()->childProfile;
        if(!$child) return redirect()->route('onboarding.edit');
        $courseIds=Enrollment::where('child_profile_id',$child->id)->where('status','active')->pluck('learning_path_id');
        $sessions=LiveSession::whereIn('learning_path_id',$courseIds)
            ->whereHas('learningPath', fn($query) => $query->where('is_published', true))
            ->where('starts_at','>=',now()->subHours(2))
            ->with(['learningPath','instructor.instructorProfile','bookings'])
            ->orderBy('starts_at')
            ->get();
        $bookedIds=SessionBooking::where('child_profile_id',$child->id)->pluck('live_session_id');
        return view('live.index',compact('sessions','bookedIds'));
    }
    public function show(Request $request, LiveSession $liveSession){
        $child=$request->user()->childProfile; abort_unless($child,403);
        abort_unless($liveSession->learningPath,404);
        abort_unless(Enrollment::where('child_profile_id',$child->id)->where('learning_path_id',$liveSession->learning_path_id)->where('status','active')->exists(),403);
        $liveSession->load(['learningPath','instructor.instructorProfile','bookings']);
        $booking=SessionBooking::where('child_profile_id',$child->id)->where('live_session_id',$liveSession->id)->first();
        return view('live.show',compact('liveSession','booking'));
    }
    public function book(Request $request, LiveSession $liveSession){
        $child=$request->user()->childProfile; abort_unless($child,403);
        abort_unless($liveSession->learningPath,404);
        abort_unless(Enrollment::where('child_profile_id',$child->id)->where('learning_path_id',$liveSession->learning_path_id)->where('status','active')->exists(),403);
        $count=$liveSession->bookings()->where('status','booked')->count();
        abort_if($count >= $liveSession->capacity,422,'Kapasitas kelas sudah penuh.');
        SessionBooking::updateOrCreate(['live_session_id'=>$liveSession->id,'child_profile_id'=>$child->id],['status'=>'booked','booked_at'=>now()]);
        return back()->with('success','Kelas live berhasil dipesan.');
    }
}
