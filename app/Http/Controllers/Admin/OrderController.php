<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Transaction::with([
            'parent',
            'enrollments.child',
            'enrollments.course',
            'enrollment.child',
            'enrollment.course'
        ])->latest();

        if ($status && in_array($status, ['paid', 'pending', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhereHas('parent', function ($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(12)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status ?: 'all',
            'search' => $search,
            'totalCount' => Transaction::count(),
            'paidCount' => Transaction::where('status', 'paid')->count(),
            'pendingCount' => Transaction::where('status', 'pending')->count(),
            'cancelledCount' => Transaction::where('status', 'cancelled')->count(),
            'totalRevenue' => Transaction::where('status', 'paid')->sum('total'),
        ]);
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'status' => ['required', 'in:paid,pending,cancelled'],
        ]);

        $transaction->update([
            'status' => $data['status'],
            'paid_at' => $data['status'] === 'paid' ? ($transaction->paid_at ?: now()) : null,
        ]);

        foreach ($transaction->all_enrollments as $enrollment) {
            if ($data['status'] === 'paid') {
                $enrollment->update([
                    'status' => 'active',
                    'enrolled_at' => $enrollment->enrolled_at ?: now(),
                ]);
            } elseif ($data['status'] === 'cancelled') {
                $enrollment->update(['status' => 'cancelled']);
            }
        }

        return back()->with('success', "Status invoice {$transaction->invoice_code} berhasil diperbarui.");
    }
}
