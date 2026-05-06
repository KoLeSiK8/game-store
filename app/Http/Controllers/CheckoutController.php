<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Game;
use App\Models\Order;
use App\Models\Payment;
use App\Services\ActivityLogger;
use App\Services\CartService;
use App\Services\LibraryService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Create a pending order from a single game or the current cart.
     */
    public function createOrder(
        CreateOrderRequest $request,
        CartService $cartService,
        LibraryService $libraryService,
        OrderService $orderService
    ): RedirectResponse {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($user->is_banned) {
            return back()->withErrors([
                'purchase' => 'Ваш аккаунт заблокирован. Покупка недоступна.',
            ]);
        }

        $items = $this->resolveOrderItems($request, $cartService);

        if (empty($items)) {
            return back()->withErrors([
                'purchase' => 'Не удалось сформировать заказ. Корзина пуста.',
            ]);
        }

        foreach ($items as $item) {
            if ($item['game']->status !== 'approved') {
                return back()->withErrors([
                    'purchase' => 'Одна из выбранных игр сейчас недоступна для покупки.',
                ]);
            }

            if ($libraryService->hasGame($user, $item['game'])) {
                return back()->withErrors([
                    'purchase' => 'Одна из выбранных игр уже есть в вашей библиотеке.',
                ]);
            }
        }

        $order = $orderService->createOrder($user, $items);

        return redirect()->route('checkout.show', $order);
    }

    /**
     * Show the protected checkout page.
     */
    public function show(Order $order): View
    {
        $user = request()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $order->load(['items.game', 'payment']);

        return view('checkout.index', [
            'order' => $order,
        ]);
    }

    /**
     * Process fake payment for the order.
     */
    public function pay(
        ProcessPaymentRequest $request,
        Order $order,
        PaymentService $paymentService,
        LibraryService $libraryService,
        ActivityLogger $activityLogger,
        CartService $cartService
    ): RedirectResponse {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        if ($user->is_banned) {
            return back()->withErrors([
                'payment' => 'Ваш аккаунт заблокирован. Оплата недоступна.',
            ]);
        }

        if (!in_array($order->status, ['pending', 'failed'], true)) {
            return back()->withErrors([
                'payment' => 'Этот заказ уже был обработан.',
            ]);
        }

        $order->load(['items.game']);

        try {
            $paymentStatus = DB::transaction(function () use (
                $request,
                $order,
                $paymentService,
                $libraryService,
                $activityLogger,
                $user
            ) {
                $normalizedCard = preg_replace('/\D+/', '', $request->string('card_number')->toString()) ?? '';

                $payment = Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $user->id,
                        'amount' => $order->total_amount,
                        'currency' => $order->currency,
                        'payment_method' => 'card',
                        'card_last4' => substr($normalizedCard, -4),
                        'status' => 'pending',
                        'transaction_id' => (string) Str::uuid(),
                    ]
                );

                $status = $paymentService->processFakePayment($order, $normalizedCard);

                $payment->update([
                    'status' => $status,
                ]);

                if ($status === 'completed') {
                    $order->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    foreach ($order->items as $item) {
                        $libraryService->addGameToLibrary($user, $item->game, $item->id);
                        $activityLogger->logPurchase($user, $item->game);
                    }

                    return 'completed';
                }

                $order->update([
                    'status' => 'failed',
                ]);

                return 'failed';
            });
        } catch (Throwable $exception) {
            Log::error('Checkout payment failed with exception.', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);

            $order->update([
                'status' => 'failed',
            ]);

            return back()->withErrors([
                'payment' => 'Во время обработки оплаты произошла ошибка. Попробуйте еще раз.',
            ]);
        }

        if ($paymentStatus === 'completed') {
            foreach ($order->items as $item) {
                $cartService->removeGame($item->game_id, $user);
            }

            Log::info('Fake payment completed successfully.', [
                'order_id' => $order->id,
                'user_id' => $user->id,
            ]);

            return redirect()->route('orders.success', $order);
        }

        Log::warning('Fake payment failed.', [
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('orders.failed', $order);
    }

    /**
     * Show success page for a paid order.
     */
    public function success(Order $order): View
    {
        $user = request()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $order->load(['items.game', 'payment']);

        return view('orders.success', [
            'order' => $order,
        ]);
    }

    /**
     * Show failed payment page.
     */
    public function failed(Order $order): View
    {
        $user = request()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $order->load(['items.game', 'payment']);

        return view('orders.failed', [
            'order' => $order,
        ]);
    }

    /**
     * Resolve order items either from direct purchase or from the cart.
     *
     * @return array<int, array{game: Game, quantity: int}>
     */
    private function resolveOrderItems(CreateOrderRequest $request, CartService $cartService): array
    {
        $gameId = $request->integer('game_id');

        if ($gameId > 0) {
            $game = Game::where('status', 'approved')->findOrFail($gameId);

            return [[
                'game' => $game,
                'quantity' => 1,
            ]];
        }

        return $cartService->getCartItems($request->user());
    }
}
