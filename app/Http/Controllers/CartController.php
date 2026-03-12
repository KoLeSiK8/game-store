<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Показ корзины.
     */
    public function view(CartService $cartService)
    {
        return view('cart.view', [
            'items' => $cartService->getCartItems(request()->user()),
        ]);
    }

    /**
     * Добавить игру в корзину.
     */
    public function add(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $cartService->addGame(
            (int) $validated['game_id'],
            (int) ($validated['quantity'] ?? 1),
            $request->user()
        );

        return back()->with('status', 'Игра добавлена в корзину.');
    }

    /**
     * Удалить игру из корзины.
     */
    public function remove(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
        ]);

        $cartService->removeGame((int) $validated['game_id'], $request->user());

        return back()->with('status', 'Игра удалена из корзины.');
    }

    /**
     * Переключить игру в корзине (добавить/удалить).
     */
    public function toggle(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
        ]);

        $user = $request->user();
        $cart = $cartService->getCart($user);

        if (isset($cart[(int) $validated['game_id']])) {
            $cartService->removeGame((int) $validated['game_id'], $user);
            return back()->with('status', 'Игра удалена из корзины.');
        }

        $cartService->addGame((int) $validated['game_id'], 1, $user);

        return back()->with('status', 'Игра добавлена в корзину.');
    }

    /**
     * Очистить корзину.
     */
    public function clear(CartService $cartService)
    {
        $cartService->clearCart(request()->user());

        return back()->with('status', 'Корзина очищена.');
    }
}
