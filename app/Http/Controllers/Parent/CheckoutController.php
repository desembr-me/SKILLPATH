<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\CartItem; use App\Services\ScheduleConflictService; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Illuminate\Support\Str;
class CheckoutController extends Controller {
 public function index(Request $r){
  $items=CartItem::with(['child','schedule.course.category'])->where('parent_id',$r->user()->id)->latest()->get();
  if($items->isEmpty()) return redirect()->route('parent.cart')->with('error','Keranjang masih kosong.');
  $subtotal=$items->sum(fn($i)=>$i->schedule->course->price);
  $platformFee=$items->count()*15000;
  $total=$subtotal+$platformFee;
  return view('parent.checkout',compact('items','subtotal','platformFee','total'));
 }
 public function store(Request $r, ScheduleConflictService $svc){
  $items=CartItem::with(['child','schedule.course'])->where('parent_id',$r->user()->id)->get();
  if($items->isEmpty())return back()->with('error','Keranjang masih kosong.');
  $created=0;$conflicts=[];
  foreach($items as $item){
   $conf=$svc->conflicts($item->child,$item->schedule);
   if($conf){$conflicts[]=$item->schedule->course->title;continue;}
   DB::transaction(function()use($r,$item){
    $e=$item->child->enrollments()->create(['parent_id'=>$r->user()->id,'course_id'=>$item->schedule->course_id,'schedule_id'=>$item->schedule_id,'status'=>'pending_payment','enrolled_at'=>now()]);
    $e->transaction()->create(['parent_id'=>$r->user()->id,'invoice_code'=>'SP-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),'subtotal'=>$item->schedule->course->price,'platform_fee'=>15000,'total'=>$item->schedule->course->price+15000,'status'=>'pending']);
    $item->delete();
   });
   $created++;
  }
  if($created && $conflicts)return redirect()->route('parent.orders')->with('success',$created.' course berhasil di-checkout.')->with('error','Bentrok jadwal, tidak diproses: '.implode(', ',$conflicts));
  if($created)return redirect()->route('parent.orders')->with('success',$created.' course berhasil di-checkout. Lanjutkan pembayaran demo pada riwayat pesanan.');
  return back()->with('error','Semua item bentrok jadwal dengan course aktif anak: '.implode(', ',$conflicts));
 }
}
