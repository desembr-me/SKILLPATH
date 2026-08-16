<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request, ScheduleConflictService $conflictService)
    {
        $items = CartItem::with([
            'child',
            'schedule.course.category',
            'schedule.course.instructor'
        ])
        ->where('parent_id', $request->user()->id)
        ->latest()
        ->get();

        if ($items->isEmpty()) {
            return redirect()->route('parent.cart')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Attach conflict checks to each item
        $hasConflict = false;
        foreach ($items as $item) {
            $conflicts = $conflictService->conflicts($item->child, $item->schedule);
            $item->conflicts = $conflicts;
            if (!empty($conflicts)) {
                $hasConflict = true;
                $item->alternatives = $conflictService->alternatives($item->schedule);
            }
        }

        $subtotal = $items->sum(fn ($i) => (float) $i->schedule->course->price);
        $platformFee = $items->count() * 15000;
        $total = $subtotal + $platformFee;

        $paymentMethods = [
            [
                'id' => 'bca_va',
                'name' => 'BCA Virtual Account',
                'category' => 'Virtual Account',
                'badge' => 'Otomatis',
                'icon' => 'bank',
                'desc' => 'Verifikasi instan otomatis 24 jam via BCA Mobile, myBCA, & ATM'
            ],
            [
                'id' => 'mandiri_va',
                'name' => 'Mandiri Virtual Account',
                'category' => 'Virtual Account',
                'badge' => 'Otomatis',
                'icon' => 'bank',
                'desc' => 'Bayar instan via Livin by Mandiri, ATM, & Internet Banking'
            ],
            [
                'id' => 'bri_va',
                'name' => 'BRI Virtual Account (BRIVA)',
                'category' => 'Virtual Account',
                'badge' => 'Otomatis',
                'icon' => 'bank',
                'desc' => 'Bayar instan via BRImo, ATM BRI, & Agen BRILink'
            ],
            [
                'id' => 'bni_va',
                'name' => 'BNI Virtual Account',
                'category' => 'Virtual Account',
                'badge' => 'Otomatis',
                'icon' => 'bank',
                'desc' => 'Bayar instan via BNI Mobile Banking & ATM'
            ],
            [
                'id' => 'qris',
                'name' => 'QRIS (Semua E-Wallet & M-Banking)',
                'category' => 'QRIS Instan',
                'badge' => 'Instan',
                'icon' => 'qr',
                'desc' => 'Scan QR via GoPay, OVO, DANA, ShopeePay, LinkAja, & Mobile Banking'
            ],
            [
                'id' => 'bank_transfer',
                'name' => 'Transfer Bank Manual (BCA)',
                'category' => 'Transfer Manual',
                'badge' => 'Verifikasi',
                'icon' => 'wallet',
                'desc' => 'Transfer langsung ke rekening resmi PT SkillPath Edukasi Indonesia'
            ],
            [
                'id' => 'instant_demo',
                'name' => 'Simulasi Pembayaran Langsung (Demo)',
                'category' => 'Instant Demo',
                'badge' => 'Demo Cepat',
                'icon' => 'spark',
                'desc' => 'Langsung aktifkan enrollment secara instan untuk pengujian alur belajar'
            ]
        ];

        return view('parent.checkout', compact('items', 'subtotal', 'platformFee', 'total', 'paymentMethods', 'hasConflict'));
    }

    public function store(Request $request, ScheduleConflictService $conflictService)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:bca_va,mandiri_va,bri_va,bni_va,qris,bank_transfer,instant_demo'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'parent_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $items = CartItem::with([
            'child',
            'schedule.course'
        ])
        ->where('parent_id', $request->user()->id)
        ->get();

        if ($items->isEmpty()) {
            return redirect()->route('parent.cart')->with('error', 'Keranjang masih kosong.');
        }

        // Calculate voucher discount if provided
        $discount = 0;
        $voucherCode = strtoupper(trim($validated['voucher_code'] ?? ''));
        if ($voucherCode === 'SKILLHEMAT') {
            $discount = 25000;
        } elseif ($voucherCode === 'ANAKHEBAT') {
            $discount = 50000;
        }

        $createdTransactions = [];
        $conflicts = [];
        $isInstantDemo = ($validated['payment_method'] === 'instant_demo');

        foreach ($items as $item) {
            $conf = $conflictService->conflicts($item->child, $item->schedule);
            if (!empty($conf)) {
                $conflicts[] = $item->schedule->course->title . ' (' . $item->child->name . ')';
                continue;
            }

            $tx = DB::transaction(function () use ($request, $item, $validated, $isInstantDemo, $voucherCode, $discount) {
                // Determine VA number generator
                $vaPrefix = match ($validated['payment_method']) {
                    'mandiri_va' => '88908',
                    'bri_va' => '12800',
                    'bni_va' => '82770',
                    default => '80777', // BCA default
                };
                $vaNumber = $vaPrefix . str_pad((string)$request->user()->id, 4, '0', STR_PAD_LEFT) . rand(100000, 999999);

                $coursePrice = (float)$item->schedule->course->price;
                $platformFee = 15000;
                $itemTotal = max(0, $coursePrice + $platformFee - $discount);

                $enrollment = $item->child->enrollments()->create([
                    'parent_id' => $request->user()->id,
                    'course_id' => $item->schedule->course_id,
                    'schedule_id' => $item->schedule_id,
                    'status' => $isInstantDemo ? 'active' : 'pending_payment',
                    'enrolled_at' => $isInstantDemo ? now() : null,
                ]);

                $transaction = $enrollment->transaction()->create([
                    'parent_id' => $request->user()->id,
                    'invoice_code' => 'SP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'subtotal' => $coursePrice,
                    'platform_fee' => $platformFee,
                    'total' => $itemTotal,
                    'payment_method' => $validated['payment_method'],
                    'status' => $isInstantDemo ? 'paid' : 'pending',
                    'paid_at' => $isInstantDemo ? now() : null,
                    'metadata' => [
                        'child_name' => $item->child->name,
                        'course_title' => $item->schedule->course->title,
                        'schedule_day' => $item->schedule->day_of_week,
                        'schedule_time' => substr($item->schedule->start_time, 0, 5) . ' - ' . substr($item->schedule->end_time, 0, 5),
                        'location' => $item->schedule->course->location_name . ', ' . $item->schedule->course->city,
                        'va_number' => $vaNumber,
                        'notes' => $validated['parent_notes'] ?? null,
                        'voucher_code' => $voucherCode ?: null,
                        'discount' => $discount,
                    ]
                ]);

                $item->delete();

                return $transaction;
            });

            $createdTransactions[] = $tx;
        }

        $createdCount = count($createdTransactions);

        if ($createdCount === 0) {
            return back()->with('error', 'Semua item bentrok jadwal dengan course aktif anak: ' . implode(', ', $conflicts));
        }

        $flashSuccess = $isInstantDemo
            ? 'Pembayaran demo instan berhasil! Enrollment anak Anda langsung aktif.'
            : 'Checkout berhasil! Silakan selesaikan pembayaran untuk mengaktifkan kelas.';

        if ($conflicts) {
            session()->flash('error', 'Beberapa item tidak diproses karena bentrok jadwal: ' . implode(', ', $conflicts));
        }

        // If only 1 transaction created, redirect directly to the payment page
        if ($createdCount === 1) {
            return redirect()->route('parent.payment.show', $createdTransactions[0]->id)
                ->with('success', $flashSuccess);
        }

        // If multiple items checked out, redirect to orders with note
        return redirect()->route('parent.orders')
            ->with('success', "{$createdCount} course berhasil di-checkout. Silakan pilih pesanan untuk menyelesaikan pembayaran.");
    }
}
