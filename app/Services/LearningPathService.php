<?php
namespace App\Services;
use App\Models\Child; use App\Models\Course; use App\Models\LearningPath; use Illuminate\Support\Facades\DB;
class LearningPathService {
 public function regenerate(Child $child): LearningPath {
  return DB::transaction(function() use($child){
   $child->learningPaths()->where('status','active')->update(['status'=>'archived']);
   $interests=$child->interests ?: [];
   $courses=Course::with('category')->where('status','active')->where('age_min','<=',$child->age)->where('age_max','>=',$child->age)
    ->when($interests,fn($q)=>$q->whereHas('category',fn($c)=>$c->whereIn('name',$interests)))->limit(4)->get();
   if($courses->isEmpty()) $courses=Course::with('category')->where('status','active')->where('age_min','<=',$child->age)->where('age_max','>=',$child->age)->limit(4)->get();
   $path=$child->learningPaths()->create(['title'=>($interests[0]??'Explorer').' Learning Path','rationale'=>'Disusun dari minat hasil co-design, usia, dan course yang tersedia.','status'=>'active','generated_at'=>now()]);
   foreach($courses as $i=>$course){$path->items()->create(['course_id'=>$course->id,'sequence'=>$i+1,'reason'=>'Sesuai usia dan minat '.$course->category->name,'status'=>$i===0?'recommended':'locked','match_score'=>max(70,96-($i*7))]);}
   return $path->load('items.course.category');
  });
 }
}
