<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->parent_id === $request->user()->id, 403);

        $transaction->load([
            'parent',
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

        return view('parent.payment', [
            'transaction' => $transaction,
            'enrollment' => $transaction->enrollment,
            'course' => $transaction->enrollment?->course,
            'child' => $transaction->enrollment?->child,
            'schedule' => $transaction->enrollment?->schedule,
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

            if ($transaction->enrollment) {
                $transaction->enrollment->update([
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil diverifikasi! Kelas telah aktif dan anak Anda siap mulai belajar.');
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

            if ($transaction->enrollment) {
                $transaction->enrollment->update([
                    'status' => 'cancelled',
                ]);
            }
        });

        return redirect()->route('parent.orders')->with('success', "Pesanan {$transaction->invoice_code} berhasil dibatalkan.");
    }
}
