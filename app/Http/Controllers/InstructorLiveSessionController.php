<?php
namespace App\Http\Controllers;
use App\Models\LearningPath;
use App\Models\LiveSession;
use Illuminate\Http\Request;
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
    public function destroy(Request $request, LiveSession $liveSession){
        abort_unless($request->user()->role==='instructor' && $liveSession->instructor_id===$request->user()->id,403);
        $liveSession->delete();
        return back()->with('success','Jadwal live class dihapus.');
    }
}
