<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Список отзывов на модерации.
     */
    public function index()
    {
        $reviews = Review::with(['user', 'game'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reviews.index', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Одобрить отзыв.
     */
    public function approveReview(Request $request, Review $review)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $review->update([
            'status' => 'approved',
            'moderated_by' => $admin->id,
            'moderated_at' => now(),
        ]);

        return back()->with('status', 'Отзыв одобрен.');
    }

    /**
     * Удалить отзыв.
     */
    public function deleteReview(Request $request, Review $review)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $review->delete();

        return back()->with('status', 'Отзыв удален.');
    }
}