<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Review;
use App\Services\ActivityLogger;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Форма создания отзыва.
     */
    public function create(Request $request, Game $game, ReviewService $reviewService)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($user->is_banned) {
            abort(403, 'Ваш аккаунт заблокирован. Отзывы недоступны.');
        }

        if (!$reviewService->canReview($user, $game)) {
            abort(403, 'Forbidden');
        }

        return view('reviews.create', [
            'game' => $game,
        ]);
    }

    /**
     * Сохранение отзыва.
     */
    public function store(Request $request, Game $game, ReviewService $reviewService, ActivityLogger $activityLogger)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($user->is_banned) {
            abort(403, 'Ваш аккаунт заблокирован. Отзывы недоступны.');
        }

        if (!$reviewService->canReview($user, $game)) {
            abort(403, 'Forbidden');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = $reviewService->createReview(
            $user,
            $game,
            (int) $validated['rating'],
            $validated['content'] ?? null
        );

        $activityLogger->logReview($user, $game);

        return redirect('/profile')->with('status', 'Отзыв отправлен на модерацию.');
    }

    /**
     * Удаление отзыва.
     */
    public function delete(Request $request, Review $review)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($review->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $review->delete();

        return back()->with('status', 'Отзыв удален.');
    }
}
