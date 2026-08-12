<?php
namespace App\Http\Controllers;
use App\Models\LearningPath;
use Illuminate\Http\Request;
class CartController extends Controller
{
    public function index(Request $request){
        $ids = collect($request->session()->get('cart', []))->unique()->values();
        $courses = LearningPath::whereIn('id',$ids)->with(['skill','instructor.instructorProfile'])->get();
        $subtotal = $courses->sum(fn($c)=>$c->effectivePrice());
        return view('cart.index', compact('courses','subtotal'));
    }
    public function add(Request $request, LearningPath $learningPath){
        abort_unless($learningPath->is_published,404);
        if ($learningPath->is_free || $learningPath->effectivePrice() <= 0) return redirect()->route('courses.show',$learningPath)->with('success','Course ini gratis. Aktifkan langsung dari halaman course.');
        $cart = collect($request->session()->get('cart', []))->push($learningPath->id)->unique()->values()->all();
        $request->session()->put('cart',$cart);
        return back()->with('success','Course ditambahkan ke keranjang.');
    }
    public function remove(Request $request, LearningPath $learningPath){
        $cart = collect($request->session()->get('cart', []))->reject(fn($id)=>(int)$id === $learningPath->id)->values()->all();
        $request->session()->put('cart',$cart);
        return back()->with('success','Course dihapus dari keranjang.');
    }
}
