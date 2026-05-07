@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Админ-панель</h2>
        <p class="muted">Модерация контента, заявок продавцов и управление пользователями.</p>
    </section>

    <section class="grid grid-3">
        <div class="card">
            <h3 style="margin-top: 0;">Игры</h3>
            <p class="muted">Одобрение и отклонение игр продавцов.</p>
            <a class="pill" href="{{ route('admin.games.index') }}">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Отзывы</h3>
            <p class="muted">Модерация отзывов пользователей.</p>
            <a class="pill" href="{{ route('admin.reviews.index') }}">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Пользователи</h3>
            <p class="muted">Роли, блокировки, управление доступом.</p>
            <a class="pill" href="{{ route('admin.users.index') }}">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Заявки продавцов</h3>
            <p class="muted">Одобрение анкет на получение статуса продавца.</p>
            <a class="pill" href="{{ route('admin.seller_requests.index') }}">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Аналитика</h3>
            <p class="muted">Продажи, топ игр и фильтры по датам.</p>
            <a class="pill" href="{{ route('analytics.index') }}">Перейти</a>
        </div>
    </section>
</main>
@endsection