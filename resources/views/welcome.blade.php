@extends('layouts.app')

@section('title', 'Game Store')

@section('content')
@php
    $user = auth()->user();
    $isSeller = $user ? $user->roles->pluck('name')->contains('seller') : false;
@endphp

<main class="grid" style="gap: 28px;">
    <section class="card" style="display: grid; gap: 20px; align-items: center;">
        <div style="display: grid; gap: 10px;">
            <div class="muted" style="text-transform: uppercase; letter-spacing: 0.12em; font-size: 12px;">
                Цифровая платформа игр
            </div>
            <h2 style="font-family: 'Sora', sans-serif; font-size: 34px; margin: 0;">
                Добро пожаловать в витрину, где каждая игра становится частью вашей библиотеки
            </h2>
            <p class="muted" style="margin: 0; font-size: 16px;">
                Каталог, покупки, библиотека, рекомендации и защищенные загрузки — всё в одном месте.
            </p>
        </div>

        @auth
            <div class="card" style="background: #eef2ff; border-color: #c7d2fe;">
                <strong>Привет, {{ $user->name }}!</strong>
                <div class="muted">Твоя библиотека, покупки и новые релизы уже доступны.</div>
                <div style="margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <a class="btn" href="{{ route('profile') }}">Перейти в профиль</a>
                    <a class="btn secondary" href="{{ route('catalog.index') }}">Открыть каталог</a>
                </div>
            </div>
        @endauth

        @guest
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a class="btn" href="{{ route('register') }}">Создать аккаунт</a>
                <a class="btn secondary" href="{{ route('login') }}">Войти</a>
            </div>
        @endguest

        <div class="card" style="display: grid; gap: 10px;">
            <label class="muted" for="search">Поиск по каталогу</label>
            <form method="GET" action="{{ route('catalog.index') }}" style="display: grid; gap: 10px;">
                <input id="search" name="q" class="input" placeholder="Название, тег, категория...">
                <button class="btn" type="submit">Искать</button>
            </form>
        </div>
    </section>

    <section class="grid grid-3">
        <div class="card" style="background: linear-gradient(135deg, #1d4ed8, #0ea5e9); color: #fff;">
            <h3 style="margin-top: 0;">Лидеры продаж</h3>
            <p style="opacity: 0.9;">Самые популярные тайтлы этой недели.</p>
            <a class="pill" href="{{ route('catalog.index') }}" style="background: #fff; color: #0f172a;">Смотреть</a>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f59e0b, #f97316); color: #fff;">
            <h3 style="margin-top: 0;">Новинки недели</h3>
            <p style="opacity: 0.9;">Свежие релизы и инди-хиты.</p>
            <a class="pill" href="{{ route('catalog.index') }}" style="background: #fff; color: #0f172a;">Смотреть</a>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #10b981, #22c55e); color: #fff;">
            <h3 style="margin-top: 0;">Рекомендуем</h3>
            <p style="opacity: 0.9;">Подборки на основе ваших интересов.</p>
            <a class="pill" href="{{ route('recommendations.index') }}" style="background: #fff; color: #0f172a;">Смотреть</a>
        </div>
    </section>

    <section class="grid grid-2">
        <div class="card">
            <h3 style="margin-top: 0;">Почему это удобно</h3>
            <ul style="margin: 0; padding-left: 18px; color: var(--muted);">
                <li>Мгновенная покупка и доступ в библиотеке</li>
                <li>Защищенные загрузки с поддержкой докачки</li>
                <li>Отзывы только от покупателей</li>
            </ul>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Для продавцов</h3>
            <p class="muted">Загрузка версий игр, аналитика продаж, модерация и управление релизами в одном кабинете.</p>
            <div style="margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
                @auth
                    @if ($isSeller)
                        <a class="btn" href="{{ route('seller.dashboard') }}">Личный кабинет продавца</a>
                    @else
                        <a class="btn" href="{{ route('seller.request.create') }}">Стать продавцом</a>
                    @endif
                @else
                    <a class="btn" href="{{ route('login') }}">Войти, чтобы стать продавцом</a>
                @endauth
            </div>
        </div>
    </section>
</main>
@endsection