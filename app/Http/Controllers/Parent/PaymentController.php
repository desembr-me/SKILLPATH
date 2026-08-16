<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $pendingTx = Transaction::where('parent_id', $request->user()->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingTx) {
            return redirect()->route('parent.payment.show', $pendingTx);
        }

        $latestTx = Transaction::where('parent_id', $request->user()->id)
            ->latest()
            ->first();

        if ($latestTx) {
            return redirect()->route('parent.payment.show', $latestTx);
        }

        return redirect()->route('parent.orders')->with('info', 'Belum ada transaksi pembayaran aktif.');
    }

    public function show(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->parent_id === $request->user()->id, 403);

        $transaction->load([
            'parent',
            'enrollments.child',
            'enrollments.course.category',
            'enrollments.course.instructor',
            'enrollments.schedule',
            'enrollment.child',
            'enrollment.course.category',
            'enrollment.course.instructor',
            'enrollment.schedule',
        ]);

        $expiresAt = $transaction->created_at->copy()->addHours(24);
        $isExpired = now()->greaterThan($expiresAt) && $transaction->status === 'pending';

        // Payment method metadata preparation
        $metadata = $transaction->metadata ?? [];
        $paymentMethod = $transaction->payment_method ?: 'bca_va';

        if (empty($metadata['va_number'])) {
            $vaPrefix = match ($paymentMethod) {
                'mandiri_va' => '88908',
                'bri_va' => '12800',
                'bni_va' => '82770',
                default => '80777',
            };
            $metadata['va_number'] = $vaPrefix . str_pad((string)$request->user()->id, 4, '0', STR_PAD_LEFT) . rand(100000, 999999);
            $transaction->update(['metadata' => $metadata]);
        }

        $allEnrollments = $transaction->all_enrollments;
        $primaryEnrollment = $allEnrollments->first() ?: $transaction->enrollment;

        return view('parent.payment', [
            'transaction' => $transaction,
            'enrollments' => $allEnrollments,
            'isMultiCourse' => $allEnrollments->count() > 1,
            'enrollment' => $primaryEnrollment,
            'course' => $primaryEnrollment?->course,
            'child' => $primaryEnrollment?->child,
            'schedule' => $primaryEnrollment?->schedule,
            'expiresAt' => $expiresAt,
            'isExpired' => $isExpired,
            'metadata' => $metadata,
        ]);
    }

    public function pay(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->parent_id === $request->user()->id, 403);

        if ($transaction->status === 'paid') {
            return back()->with('info', 'Transaksi ini sudah lunas.');
        }

        if ($transaction->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan.');
        }

        $paymentMethod = $request->input('payment_method', $transaction->payment_method ?: 'bca_va');

        DB::transaction(function () use ($transaction, $paymentMethod) {
            $transaction->update([
                'status' => 'paid',
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
            ]);

            // Activate all enrollments for this transaction
            foreach ($transaction->all_enrollments as $enr) {
                $enr->update([
                    'status' => 'active',
                    'enrolled_at' => $enr->enrolled_at ?: now(),
                ]);
            }
        });

        $count = $transaction->all_enrollments->count();
        $msg = $count > 1
            ? "Pembayaran berhasil diverifikasi! Seluruh {$count} kelas telah aktif dan anak Anda siap mulai belajar."
            : "Pembayaran berhasil diverifikasi! Kelas telah aktif dan anak Anda siap mulai belajar.";

        return back()->with('success', $msg);
    }

    public function cancel(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->parent_id === $request->user()->id, 403);

        if ($transaction->status === 'paid') {
            return back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status' => 'cancelled',
            ]);

            // Cancel all enrollments for this transaction
            foreach ($transaction->all_enrollments as $enr) {
                $enr->update([
                    'status' => 'cancelled',
                ]);
            }
        });

        return redirect()->route('parent.orders')->with('success', "Pesanan {$transaction->invoice_code} berhasil dibatalkan.");
    }
}
