<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\ActivityLogger;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Оформление заказа (форма + создание).
     */
    public function checkout(Request $request, CartService $cartService, OrderService $orderService, ActivityLogger $activityLogger)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($user->is_banned) {
            abort(403, 'Ваш аккаунт заблокирован. Покупки недоступны.');
        }

        $items = $cartService->getCartItems($user);

        if ($request->isMethod('post')) {
            if (empty($items)) {
                return back()->withErrors(['cart' => 'Корзина пуста.']);
            }

            $order = $orderService->createOrder($user, $items, $activityLogger);
            $orderService->completeOrder($order);
            $cartService->clearCart($user);

            return redirect()->route('orders.details', $order->id)
                ->with('status', 'Заказ успешно оформлен.');
        }

        return view('orders.checkout', [
            'items' => $items,
            'total' => $orderService->calculateTotal($items),
        ]);
    }

    /**
     * История заказов пользователя.
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $orders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('orders.history', [
            'orders' => $orders,
        ]);
    }

    /**
     * Детали заказа.
     */
    public function orderDetails(Request $request, Order $order)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $order->load(['items.game']);

        return view('orders.details', [
            'order' => $order,
        ]);
    }
}
