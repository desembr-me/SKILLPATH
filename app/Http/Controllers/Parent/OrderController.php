<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $parent = $request->user();

        $query = $parent->transactions()
            ->with([
                'enrollment.course.category',
                'enrollment.course.instructor',
                'enrollment.child',
                'enrollment.schedule'
            ])
            ->latest();

        if ($status && in_array($status, ['pending', 'paid', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhereHas('enrollment.course', function ($c) use ($search) {
                      $c->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('enrollment.child', function ($ch) use ($search) {
                      $ch->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(8)->withQueryString();

        $allCount = $parent->transactions()->count();
        $pendingCount = $parent->transactions()->where('status', 'pending')->count();
        $paidCount = $parent->transactions()->where('status', 'paid')->count();
        $cancelledCount = $parent->transactions()->where('status', 'cancelled')->count();

        return view('parent.orders', compact(
            'orders',
            'status',
            'search',
            'allCount',
            'pendingCount',
            'paidCount',
            'cancelledCount'
        ));
    }
}
