<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    public function index(Request $request){
        $orders=Order::where('user_id',$request->user()->id)->with('items.learningPath')->latest()->get();
        return view('orders.index',compact('orders'));
    }
    public function show(Request $request, Order $order){
        abort_unless($order->user_id === $request->user()->id,403);
        $order->load('items.learningPath.instructor');
        return view('orders.show',compact('order'));
    }
}
