@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
@php
    $user = auth()->user();
    $roleNames = $user?->roles->pluck('name') ?? collect();
    $isSeller = $roleNames->contains('seller');
@endphp

<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 760px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Профиль</h2>

        @auth
            <div class="grid" style="gap: 10px;">
                <div><strong>Имя:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>
                <div><strong>Роли:</strong> {{ $roleNames->implode(', ') ?: 'user' }}</div>
                <div><strong>Статус:</strong> {{ $user->is_banned ? 'Заблокирован' : 'Активен' }}</div>
            </div>

            <div class="card" style="margin-top: 16px;">
                <div class="muted">Здесь собраны данные аккаунта, история покупок, библиотека и доступ к дополнительным разделам.</div>
            </div>

            <div style="margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
                <a class="btn" href="{{ route('orders.history') }}">История покупок</a>
                <a class="pill" href="{{ route('library.index') }}">Моя библиотека</a>
                @if ($isSeller)
                    <a class="pill" href="{{ route('seller.dashboard') }}">Кабинет продавца</a>
                @else
                    <a class="pill" href="{{ route('seller.request.create') }}">Стать продавцом</a>
                @endif
            </div>
        @endauth

        @guest
            <div class="muted">Сначала нужно войти в аккаунт.</div>
            <div style="margin-top: 12px;">
                <a class="btn" href="{{ route('login') }}">Войти</a>
            </div>
        @endguest
    </section>
</main>
@endsection