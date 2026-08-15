<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use Illuminate\Http\Request;
class OrderController extends Controller { public function index(Request $r){$orders=$r->user()->transactions()->with(['enrollment.course','enrollment.child'])->latest()->paginate(10);return view('parent.orders',compact('orders'));} }
