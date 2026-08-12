<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\LearningPath;
use App\Models\Skill;
use Illuminate\Http\Request;
class ExploreController extends Controller
{
    public function index(Request $request){
        $validated=$request->validate([
            'q'=>['nullable','string','max:80'],'category'=>['nullable','string','max:80'],'age'=>['nullable','integer','between:5,14'],
            'skill'=>['nullable','string','max:100'],'sort'=>['nullable','in:newest,rating'],
        ]);
        $query=LearningPath::query()->where('is_published',true)->with(['skill','categories','interests','instructor.instructorProfile','reviews'])->withCount(['modules','enrollments']);
        if(!empty($validated['q'])){$k=trim($validated['q']);$query->where(fn($b)=>$b->where('title','like',"%{$k}%")->orWhere('description','like',"%{$k}%")->orWhereHas('instructor',fn($i)=>$i->where('name','like',"%{$k}%")));}
        if(!empty($validated['category']))$query->whereHas('categories',fn($q)=>$q->where('slug',$validated['category']));
        if(!empty($validated['age'])){$age=(int)$validated['age'];$query->where('min_age','<=',$age)->where('max_age','>=',$age);}
        if(!empty($validated['skill']))$query->whereHas('skill',fn($q)=>$q->where('slug',$validated['skill']));
        $sort=$validated['sort']??'newest';
        $query->latest('published_at')->orderBy('title');
        $paths=$query->get();
        if($sort==='rating')$paths=$paths->sortByDesc(fn($p)=>$p->reviews->avg('rating')??0)->values();
        $categories=Category::orderBy('id')->get(); $skills=Skill::orderBy('name')->get();
        return view('explore.index',compact('paths','categories','skills'));
    }
}
