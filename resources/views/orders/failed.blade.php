@extends('layouts.app')

@section('title', 'Оплата не прошла')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fda4af;">
        <h2 style="margin-top: 0;">Оплата не прошла</h2>
        <p class="muted">Заказ #{{ $order->id }} не был оплачен. Можно попробовать снова с тестовой успешной картой.</p>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a class="btn" href="/checkout/{{ $order->id }}">Вернуться к оплате</a>
            <a class="pill" href="/cart">Вернуться в корзину</a>
        </div>
    </section>
</main>
@endsection
