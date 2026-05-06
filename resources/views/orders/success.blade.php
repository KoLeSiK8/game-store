@extends('layouts.app')

@section('title', 'Оплата прошла успешно')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #86efac;">
        <h2 style="margin-top: 0;">Оплата прошла успешно</h2>
        <p class="muted">Заказ #{{ $order->id }} оплачен. Игры уже добавлены в вашу библиотеку.</p>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a class="btn" href="/library">Открыть библиотеку</a>
            <a class="pill" href="/orders/{{ $order->id }}">Детали заказа</a>
        </div>
    </section>
</main>
@endsection
