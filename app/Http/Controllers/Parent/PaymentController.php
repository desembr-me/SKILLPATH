<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Transaction; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class PaymentController extends Controller { public function pay(Request $r,Transaction $transaction){abort_unless($transaction->parent_id===$r->user()->id,403);if($transaction->status!=='pending')return back()->with('success','Transaksi sudah diproses.');DB::transaction(function()use($transaction){$transaction->update(['status'=>'paid','payment_method'=>'demo_virtual_account','paid_at'=>now()]);$transaction->enrollment()->update(['status'=>'active','enrolled_at'=>now()]);});return back()->with('success','Pembayaran demo berhasil. Enrollment sudah aktif.');} }
