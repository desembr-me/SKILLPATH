<?php
namespace App\Http\Controllers;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class CheckoutController extends Controller
{
    public function show(Request $request){
        if (! $request->user()->childProfile) return redirect()->route('onboarding.edit');
        $ids = collect($request->session()->get('cart', []))->unique()->values();
        $courses = LearningPath::whereIn('id',$ids)->with('instructor')->get();
        if ($courses->isEmpty()) return redirect()->route('cart.index')->with('success','Keranjang masih kosong.');
        $subtotal = $courses->sum(fn($c)=>$c->effectivePrice());
        return view('checkout.index', compact('courses','subtotal'));
    }

    public function store(Request $request){
        $data = $request->validate(['payment_method'=>['required','in:bank_transfer,virtual_account,qris,ewallet']]);
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');
        $ids = collect($request->session()->get('cart', []))->unique()->values();
        $courses = LearningPath::whereIn('id',$ids)->where('is_published',true)->get();
        abort_if($courses->isEmpty(),422,'Keranjang kosong.');

        foreach ($courses as $course) {
            abort_unless($child->age >= $course->min_age && $child->age <= $course->max_age, 422, "Usia anak tidak sesuai untuk {$course->title}.");
        }

        $order = DB::transaction(function() use($request,$data,$courses){
            $subtotal = $courses->sum(fn($c)=>$c->effectivePrice());
            $order = Order::create([
                'user_id'=>$request->user()->id,
                'order_number'=>'SP-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'subtotal'=>$subtotal,'discount'=>0,'total'=>$subtotal,
                'payment_method'=>$data['payment_method'],'payment_status'=>'pending','status'=>'pending',
            ]);
            foreach($courses as $course){
                $price=(float)$course->price; $final=$course->effectivePrice();
                $order->items()->create([
                    'learning_path_id'=>$course->id,'title_snapshot'=>$course->title,
                    'price'=>$price,'discount'=>max(0,$price-$final),'final_price'=>$final,
                ]);
            }
            return $order;
        });
        $request->session()->forget('cart');
        return redirect()->route('orders.show',$order)->with('success','Pesanan dibuat. Gunakan tombol simulasi pembayaran untuk demo.');
    }

    public function pay(Request $request, Order $order){
        abort_unless($order->user_id === $request->user()->id,403);
        $child = $request->user()->childProfile;
        abort_unless($child,422,'Profil anak belum dibuat.');
        if ($order->payment_status === 'paid') return back()->with('success','Pesanan sudah dibayar.');

        DB::transaction(function() use($order,$child){
            $order->load('items.learningPath');
            $order->update(['payment_status'=>'paid','status'=>'completed','paid_at'=>now()]);
            foreach($order->items as $item){
                abort_if(! $item->learningPath || $item->learningPath->trashed() || ! $item->learningPath->is_published, 422, 'Salah satu course pada pesanan sudah tidak tersedia. Hubungi admin sebelum melanjutkan pembayaran.');
                Enrollment::updateOrCreate(
                    ['child_profile_id'=>$child->id,'learning_path_id'=>$item->learning_path_id],
                    ['order_item_id'=>$item->id,'status'=>'active','enrolled_at'=>now(),'expires_at'=>$item->learningPath->access_days ? now()->addDays($item->learningPath->access_days) : null]
                );
            }
        });
        return redirect()->route('my-courses.index')->with('success','Pembayaran demo berhasil. Course sudah aktif.');
    }
}
