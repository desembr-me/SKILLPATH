<?php
namespace App\Http\Controllers;
use App\Models\LearningPath;
use App\Models\Wishlist;
use Illuminate\Http\Request;
class WishlistController extends Controller
{
    public function index(Request $request){
        $items=Wishlist::where('user_id',$request->user()->id)
            ->whereHas('learningPath', fn($query) => $query->where('is_published', true))
            ->with('learningPath.skill','learningPath.instructor.instructorProfile')
            ->latest()
            ->get();
        return view('wishlist.index',compact('items'));
    }
    public function toggle(Request $request, LearningPath $learningPath){
        $item=Wishlist::where('user_id',$request->user()->id)->where('learning_path_id',$learningPath->id)->first();
        if($item){$item->delete(); return back()->with('success','Kelas dihapus dari wishlist.');}
        Wishlist::create(['user_id'=>$request->user()->id,'learning_path_id'=>$learningPath->id]);
        return back()->with('success','Kelas disimpan ke wishlist.');
    }
}
