<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Child;
use App\Models\CourseSchedule;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;

class CartController extends Controller
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

        $subtotal = $items->sum(fn ($i) => (float) $i->calculated_price);
        $platformFee = $items->count() * 15000;
        $total = $subtotal + $platformFee;

        $hasConflict = false;
        foreach ($items as $item) {
            $conflicts = $conflictService->conflicts($item->child, $item->schedule);
            $item->conflicts = $conflicts;
            if (!empty($conflicts)) {
                $hasConflict = true;
            }
        }

        return view('parent.cart', compact('items', 'subtotal', 'platformFee', 'total', 'hasConflict'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'schedule_id' => ['required', 'exists:course_schedules,id'],
            'package_duration' => ['nullable', 'integer', 'in:3,6,12'],
        ]);

        Child::where('parent_id', $request->user()->id)->findOrFail($data['child_id']);
        CourseSchedule::findOrFail($data['schedule_id']);

        $duration = (int) ($data['package_duration'] ?? 3);

        $item = CartItem::updateOrCreate(
            [
                'parent_id' => $request->user()->id,
                'child_id' => $data['child_id'],
                'schedule_id' => $data['schedule_id'],
            ],
            [
                'package_duration' => $duration,
            ]
        );

        return redirect()->route('parent.cart')->with('success', 'Course dan paket berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->parent_id === $request->user()->id, 403);

        $data = $request->validate([
            'package_duration' => ['required', 'integer', 'in:3,6,12'],
        ]);

        $cartItem->update(['package_duration' => (int) $data['package_duration']]);

        return back()->with('success', 'Pilihan paket kursus berhasil diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->parent_id === $request->user()->id, 403);
        $cartItem->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
