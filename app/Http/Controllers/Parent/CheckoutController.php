<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Enrollment;
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

        $subtotal = $items->sum(fn ($i) => (float) $i->calculated_price);
        $platformFee = $items->count() * 15000;
        $total = $subtotal + $platformFee;

        $paymentMethods = [
            'virtual_account' => [
                'title' => 'Virtual Account (Verifikasi Otomatis)',
                'subtitle' => 'Konfirmasi instan 24 jam via m-Banking, ATM, & Internet Banking',
                'icon' => 'bank',
                'items' => [
                    [
                        'id' => 'bca_va',
                        'name' => 'BCA Virtual Account',
                        'brand' => 'bca',
                        'brand_label' => 'BCA',
                        'badge' => 'Otomatis',
                        'badge_type' => 'auto',
                        'desc' => 'Bebas biaya admin transfer BCA',
                    ],
                    [
                        'id' => 'mandiri_va',
                        'name' => 'Mandiri Virtual Account',
                        'brand' => 'mandiri',
                        'brand_label' => 'MANDIRI',
                        'badge' => 'Otomatis',
                        'badge_type' => 'auto',
                        'desc' => 'Bayar via Livin by Mandiri & ATM Mandiri',
                    ],
                    [
                        'id' => 'bri_va',
                        'name' => 'BRI Virtual Account (BRIVA)',
                        'brand' => 'bri',
                        'brand_label' => 'BRI',
                        'badge' => 'Otomatis',
                        'badge_type' => 'auto',
                        'desc' => 'Bayar via BRImo, ATM BRI, & Agen BRILink',
                    ],
                    [
                        'id' => 'bni_va',
                        'name' => 'BNI Virtual Account',
                        'brand' => 'bni',
                        'brand_label' => 'BNI',
                        'badge' => 'Otomatis',
                        'badge_type' => 'auto',
                        'desc' => 'Bayar via BNI Mobile Banking & ATM BNI',
                    ],
                ]
            ],
            'qris' => [
                'title' => 'QRIS & Dompet Digital (E-Wallet)',
                'subtitle' => 'Scan QR praktis dari aplikasi perbankan & e-wallet apapun',
                'icon' => 'qr',
                'items' => [
                    [
                        'id' => 'qris',
                        'name' => 'QRIS Instant Scan',
                        'brand' => 'qris',
                        'brand_label' => 'QRIS',
                        'badge' => 'Instan',
                        'badge_type' => 'instant',
                        'desc' => 'Scan via GoPay, OVO, DANA, ShopeePay, LinkAja, BCA Mobile, dll',
                        'ewallet_tags' => ['GoPay', 'OVO', 'DANA', 'ShopeePay', 'LinkAja']
                    ],
                ]
            ],
            'other' => [
                'title' => 'Transfer Manual & Mode Simulasi',
                'subtitle' => 'Transfer ke rekening resmi atau pengujian alur belajar instan',
                'icon' => 'wallet',
                'items' => [
                    [
                        'id' => 'bank_transfer',
                        'name' => 'Transfer Bank Manual (BCA)',
                        'brand' => 'manual',
                        'brand_label' => 'MANUAL',
                        'badge' => 'Verifikasi',
                        'badge_type' => 'manual',
                        'desc' => 'Transfer rekening resmi PT SkillPath Edukasi Indonesia',
                    ],
                    [
                        'id' => 'instant_demo',
                        'name' => 'Simulasi Bayar Langsung (Demo)',
                        'brand' => 'demo',
                        'brand_label' => '⚡ DEMO',
                        'badge' => 'Demo Cepat',
                        'badge_type' => 'demo',
                        'desc' => 'Langsung aktifkan kelas anak untuk pengujian alur belajar',
                    ],
                ]
            ],
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
            'schedule.course.category',
            'schedule.course.instructor'
        ])
        ->where('parent_id', $request->user()->id)
        ->get();

        if ($items->isEmpty()) {
            return redirect()->route('parent.cart')->with('error', 'Keranjang masih kosong.');
        }

        // Filter valid items without schedule conflicts
        $conflicts = [];
        $validItems = collect();

        foreach ($items as $item) {
            $conf = $conflictService->conflicts($item->child, $item->schedule);
            if (!empty($conf)) {
                $conflicts[] = $item->schedule->course->title . ' (' . $item->child->name . ')';
            } else {
                $validItems->push($item);
            }
        }

        if ($validItems->isEmpty()) {
            return back()->with('error', 'Semua item di keranjang bentrok jadwal dengan kursus aktif anak: ' . implode(', ', $conflicts));
        }

        // Calculate voucher discount if provided
        $discount = 0;
        $voucherCode = strtoupper(trim($validated['voucher_code'] ?? ''));
        if ($voucherCode === 'SKILLHEMAT') {
            $discount = 25000;
        } elseif ($voucherCode === 'ANAKHEBAT') {
            $discount = 50000;
        }

        $isInstantDemo = ($validated['payment_method'] === 'instant_demo');
        $subtotal = $validItems->sum(fn ($i) => (float) $i->package_info['price']);
        $platformFee = $validItems->count() * 15000;
        $total = max(0, $subtotal + $platformFee - $discount);

        // Determine VA prefix & number
        $vaPrefix = match ($validated['payment_method']) {
            'mandiri_va' => '88908',
            'bri_va' => '12800',
            'bni_va' => '82770',
            default => '80777', // BCA default
        };
        $vaNumber = $vaPrefix . str_pad((string)$request->user()->id, 4, '0', STR_PAD_LEFT) . rand(100000, 999999);

        // Prepare metadata breakdown for every item in transaction
        $itemsMetadata = [];
        foreach ($validItems as $item) {
            $pkg = $item->package_info;
            $itemsMetadata[] = [
                'child_id' => $item->child_id,
                'child_name' => $item->child->name,
                'course_id' => $item->schedule->course_id,
                'course_title' => $item->schedule->course->title,
                'category_name' => $item->schedule->course->category->name ?? 'Kursus',
                'package_title' => $pkg['title'],
                'package_duration' => $pkg['duration_months'],
                'package_sessions' => $pkg['sessions'],
                'price' => (float) $pkg['price'],
                'original_price' => $pkg['original_price'],
                'savings' => $pkg['savings'],
                'schedule_day' => $item->schedule->day_name,
                'schedule_time' => substr($item->schedule->start_time, 0, 5) . ' - ' . substr($item->schedule->end_time, 0, 5),
                'location' => $item->schedule->course->location_name . ', ' . $item->schedule->course->city,
            ];
        }

        $firstItem = $validItems->first();
        $firstPkg = $firstItem->package_info;

        $transaction = DB::transaction(function () use ($request, $validItems, $validated, $isInstantDemo, $subtotal, $platformFee, $total, $itemsMetadata, $firstItem, $firstPkg, $vaNumber, $voucherCode, $discount) {
            // 1. Create single combined Transaction
            $transaction = Transaction::create([
                'parent_id' => $request->user()->id,
                'invoice_code' => 'SP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'subtotal' => $subtotal,
                'platform_fee' => $platformFee,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => $isInstantDemo ? 'paid' : 'pending',
                'paid_at' => $isInstantDemo ? now() : null,
                'metadata' => [
                    'is_multi' => $validItems->count() > 1,
                    'item_count' => $validItems->count(),
                    'items' => $itemsMetadata,
                    'child_name' => $firstItem->child->name,
                    'course_title' => $firstItem->schedule->course->title,
                    'package_title' => $firstPkg['title'],
                    'package_duration' => $firstPkg['duration_months'],
                    'package_sessions' => $firstPkg['sessions'],
                    'schedule_day' => $firstItem->schedule->day_name,
                    'schedule_time' => substr($firstItem->schedule->start_time, 0, 5) . ' - ' . substr($firstItem->schedule->end_time, 0, 5),
                    'location' => $firstItem->schedule->course->location_name . ', ' . $firstItem->schedule->course->city,
                    'va_number' => $vaNumber,
                    'notes' => $validated['parent_notes'] ?? null,
                    'voucher_code' => $voucherCode ?: null,
                    'discount' => $discount,
                ]
            ]);

            // 2. Create Enrollments associated with this transaction
            $firstEnrollmentId = null;
            foreach ($validItems as $idx => $item) {
                $pkg = $item->package_info;
                $enrollment = Enrollment::create([
                    'parent_id' => $request->user()->id,
                    'transaction_id' => $transaction->id,
                    'child_id' => $item->child_id,
                    'course_id' => $item->schedule->course_id,
                    'schedule_id' => $item->schedule_id,
                    'package_duration' => $pkg['duration_months'],
                    'total_sessions' => $pkg['sessions'],
                    'status' => $isInstantDemo ? 'active' : 'pending_payment',
                    'enrolled_at' => $isInstantDemo ? now() : null,
                ]);

                if ($idx === 0) {
                    $firstEnrollmentId = $enrollment->id;
                }
            }

            // Set primary enrollment_id for backward compatibility
            if ($firstEnrollmentId) {
                $transaction->update(['enrollment_id' => $firstEnrollmentId]);
            }

            // 3. Remove checked-out items from cart
            CartItem::whereIn('id', $validItems->pluck('id'))->delete();

            return $transaction;
        });

        $count = $validItems->count();
        $flashSuccess = $isInstantDemo
            ? "Pembayaran instan berhasil! {$count} kursus anak Anda langsung aktif."
            : "Pesanan {$count} kursus berhasil dibuat! Silakan selesaikan 1x pembayaran.";

        if ($conflicts) {
            session()->flash('error', 'Beberapa item tidak diproses karena bentrok jadwal: ' . implode(', ', $conflicts));
        }

        return redirect()->route('parent.payment.show', $transaction->id)
            ->with('success', $flashSuccess);
    }
}
