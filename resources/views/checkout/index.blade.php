@extends('layouts.app')

@section('title', 'Secure Payment')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="display: grid; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 42px; height: 42px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0f172a, #2563eb); color: #fff; font-size: 18px;">&#128274;</div>
            <div>
                <h2 style="margin: 0;">Secure Payment</h2>
                <div class="muted">Безопасная фиктивная оплата для демонстрации интернет-магазина.</div>
            </div>
        </div>
    </section>

    <section class="grid grid-2" style="align-items: start;">
        <div class="card">
            <h3 style="margin-top: 0;">Заказ #{{ $order->id }}</h3>
            <div class="muted" style="margin-bottom: 12px;">Статус заказа: {{ $order->status }}</div>
            <div class="grid" style="gap: 10px;">
                @foreach ($order->items as $item)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>{{ $item->game->title }}</strong>
                            <div class="muted">{{ $item->quantity }} x {{ number_format($item->price, 2, '.', ' ') }} {{ $item->currency }}</div>
                        </div>
                        <strong>{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} {{ $item->currency }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="card" style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center; background: #eff6ff; border-color: #bfdbfe;">
                <strong>Итого</strong>
                <strong>{{ number_format($order->total_amount, 2, '.', ' ') }} {{ $order->currency }}</strong>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(180deg, #ffffff, #f8fafc);">
            <h3 style="margin-top: 0;">Оплата картой</h3>
            <p class="muted" style="margin-top: 0;">Тестовые карты: 1111111111111111 для успеха и 0000000000000000 для ошибки.</p>

            @if ($errors->any())
                <div class="card" style="border-color: #fecaca; background: #fef2f2; margin-bottom: 12px;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($order->status === 'completed')
                <div class="card" style="background: #ecfdf5; border-color: #86efac;">
                    Заказ уже успешно оплачен. Игры доступны в библиотеке.
                </div>
                <a class="btn" href="/library">Перейти в библиотеку</a>
            @else
                <form method="POST" action="/checkout/{{ $order->id }}/pay" style="display: grid; gap: 12px;">
                    @csrf

                    <div class="field">
                        <label for="cardholder_name">Имя владельца карты</label>
                        <input class="input" id="cardholder_name" name="cardholder_name" type="text" value="{{ old('cardholder_name') }}" required>
                    </div>

                    <div class="field">
                        <label for="card_number">Номер карты</label>
                        <input class="input" id="card_number" name="card_number" type="text" value="{{ old('card_number') }}" placeholder="1111 1111 1111 1111" required>
                    </div>

                    <div class="grid grid-2">
                        <div class="field">
                            <label for="expiry_date">Срок действия</label>
                            <input class="input" id="expiry_date" name="expiry_date" type="text" value="{{ old('expiry_date') }}" placeholder="MM/YY" required>
                        </div>

                        <div class="field">
                            <label for="cvv">CVV</label>
                            <input class="input" id="cvv" name="cvv" type="password" value="{{ old('cvv') }}" placeholder="123" required>
                        </div>
                    </div>

                    <button class="btn" type="submit">Оплатить {{ number_format($order->total_amount, 2, '.', ' ') }} {{ $order->currency }}</button>
                </form>
            @endif
        </div>
    </section>
</main>
@endsection
