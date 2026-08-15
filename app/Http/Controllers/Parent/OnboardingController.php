<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Child; use App\Services\LearningPathService; use Illuminate\Http\Request;
class OnboardingController extends Controller {
 public function create(){return view('parent.onboarding');}
 public function store(Request $r, LearningPathService $service){$d=$r->validate(['name'=>'required|max:100','birth_date'=>'required|date|before:today','interests'=>'required|array|min:1|max:3','interests.*'=>'in:Arts,Music,Languages,Sports,Self Improvement,Technology','learning_preferences'=>'nullable|array','child_voice'=>'nullable|max:1000','discussed_with_child'=>'required|accepted']);$child=$r->user()->children()->create(['name'=>$d['name'],'birth_date'=>$d['birth_date'],'interests'=>$d['interests'],'learning_preferences'=>$d['learning_preferences']??[]]);$child->coDesignSessions()->create(['parent_id'=>$r->user()->id,'child_choices'=>$d['interests'],'agreed_interests'=>$d['interests'],'learning_preferences'=>$d['learning_preferences']??[],'parent_observations'=>['child_voice'=>$d['child_voice']??null,'discussed_with_child'=>true],'completed_at'=>now()]);$service->regenerate($child);return redirect()->route('parent.learning-path',$child)->with('success','Profil minat dan jalur belajar berhasil dibuat.');}
}
