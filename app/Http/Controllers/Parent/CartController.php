<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\CartItem; use App\Models\Child; use App\Models\CourseSchedule; use Illuminate\Http\Request;
class CartController extends Controller {
 public function index(Request $r){$items=CartItem::with(['child','schedule.course.category'])->where('parent_id',$r->user()->id)->latest()->get();$total=$items->sum(fn($i)=>$i->schedule->course->price+15000);return view('parent.cart',compact('items','total'));}
 public function store(Request $r){$d=$r->validate(['child_id'=>'required|exists:children,id','schedule_id'=>'required|exists:course_schedules,id']);Child::where('parent_id',$r->user()->id)->findOrFail($d['child_id']);CourseSchedule::findOrFail($d['schedule_id']);CartItem::firstOrCreate(['parent_id'=>$r->user()->id,'child_id'=>$d['child_id'],'schedule_id'=>$d['schedule_id']]);return redirect()->route('parent.cart')->with('success','Course ditambahkan ke keranjang.');}
 public function destroy(Request $r,CartItem $cartItem){abort_unless($cartItem->parent_id===$r->user()->id,403);$cartItem->delete();return back()->with('success','Item dihapus dari keranjang.');}
}
