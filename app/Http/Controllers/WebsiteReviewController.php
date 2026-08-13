<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WebsiteReview;
use Illuminate\Http\Request;

class WebsiteReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($request->user()->id === $order->user_id, 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        WebsiteReview::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data + ['is_published' => true]
        );

        return back()->with('success', 'Terima kasih, review website Anda berhasil dikirim.');
    }
}
