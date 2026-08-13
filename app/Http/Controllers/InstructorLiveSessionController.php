<?php
namespace App\Http\Controllers;
use App\Models\LearningPath;
use App\Models\LiveSession;
use App\Models\User;
use App\Notifications\LiveSessionRescheduled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
class InstructorLiveSessionController extends Controller
{
    public function store(Request $request, LearningPath $learningPath){
        abort_unless($request->user()->role==='instructor' && $learningPath->instructor_id===$request->user()->id,403);
        $data=$request->validate([
            'title'=>['required','string','max:150'],'description'=>['nullable','string','max:1000'],
            'starts_at'=>['required','date','after:now'],'ends_at'=>['required','date','after:starts_at'],
            'meeting_url'=>['nullable','url','max:255'],'capacity'=>['required','integer','between:1,100'],
        ]);
        $learningPath->liveSessions()->create($data+['instructor_id'=>$request->user()->id,'status'=>'scheduled']);
        $learningPath->update(['live_class_enabled'=>true]);
        return back()->with('success','Jadwal live class berhasil dibuat.');
    }

    public function update(Request $request, LiveSession $liveSession){
        abort_unless($request->user()->role==='instructor' && $liveSession->instructor_id===$request->user()->id,403);
        $data=$request->validate([
            'title'=>['required','string','max:150'],'description'=>['nullable','string','max:1000'],
            'starts_at'=>['required','date'],'ends_at'=>['required','date','after:starts_at'],
            'meeting_url'=>['nullable','url','max:255'],'capacity'=>['required','integer','between:1,100'],
        ]);

        $oldStartsAt = $liveSession->starts_at->copy();
        $oldEndsAt = $liveSession->ends_at->copy();

        $liveSession->update($data);

        $scheduleChanged = ! $oldStartsAt->equalTo($liveSession->starts_at) || ! $oldEndsAt->equalTo($liveSession->ends_at);

        $notified = 0;
        if ($scheduleChanged) {
            $liveSession->loadMissing('learningPath');
            $parents = User::whereHas('childProfile.liveBookings', function ($query) use ($liveSession) {
                $query->where('live_session_id', $liveSession->id)->where('status', '!=', 'cancelled');
            })->get();

            Notification::send($parents, new LiveSessionRescheduled($liveSession, $oldStartsAt, $oldEndsAt));
            $notified = $parents->count();
        }

        return back()->with('success', $scheduleChanged
            ? "Jadwal berhasil diubah. Notifikasi otomatis terkirim ke {$notified} orang tua."
            : 'Detail sesi berhasil diperbarui.');
    }

    public function destroy(Request $request, LiveSession $liveSession){
        abort_unless($request->user()->role==='instructor' && $liveSession->instructor_id===$request->user()->id,403);
        $liveSession->delete();
        return back()->with('success','Jadwal live class dihapus.');
    }
}
