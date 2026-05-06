<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Show user's order history.
     */
    public function history(Request $request): View
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $orders = Order::with('payment')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('orders.history', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show details for a single order.
     */
    public function orderDetails(Request $request, Order $order): View
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $order->load(['items.game', 'payment']);

        return view('orders.details', [
            'order' => $order,
        ]);
    }
}
