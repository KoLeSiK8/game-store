@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Админ-панель</h2>
        <p class="muted">Модерация контента и управление пользователями.</p>
    </section>

    <section class="grid grid-3">
        <div class="card">
            <h3 style="margin-top: 0;">Игры</h3>
            <p class="muted">Одобрение и отклонение игр продавцов.</p>
            <a class="pill" href="/admin/games">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Отзывы</h3>
            <p class="muted">Модерация отзывов пользователей.</p>
            <a class="pill" href="/admin/reviews">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Пользователи</h3>
            <p class="muted">Роли, блокировки, управление.</p>
            <a class="pill" href="/admin/users">Перейти</a>
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Аналитика</h3>
            <p class="muted">Продажи, топ игр, фильтры по датам.</p>
            <a class="pill" href="/analytics">Перейти</a>
        </div>
    </section>
</main>
@endsection
