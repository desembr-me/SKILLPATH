<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Course; use App\Models\Wishlist; use Illuminate\Http\Request;
class WishlistController extends Controller {
 public function index(Request $r){$wishlists=Wishlist::with(['course.category','course.instructor'])->where('parent_id',$r->user()->id)->latest()->get();return view('parent.wishlist',compact('wishlists'));}
 public function toggle(Request $r,Course $course){$existing=Wishlist::where('parent_id',$r->user()->id)->where('course_id',$course->id)->first();if($existing){$existing->delete();return back()->with('success','Course dihapus dari wishlist.');}Wishlist::create(['parent_id'=>$r->user()->id,'course_id'=>$course->id]);return back()->with('success','Course ditambahkan ke wishlist.');}
}
