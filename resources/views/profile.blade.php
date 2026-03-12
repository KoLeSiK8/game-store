@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 700px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Профиль</h2>

        @auth
            <div class="grid" style="gap: 10px;">
                <div><strong>Имя:</strong> {{ auth()->user()->name }}</div>
                <div><strong>Email:</strong> {{ auth()->user()->email }}</div>
                <div>
                    <strong>Роли:</strong>
                    {{ auth()->user()->roles->pluck('name')->implode(', ') ?: 'user' }}
                </div>
                <div>
                    <strong>Статус:</strong>
                    {{ auth()->user()->is_banned ? 'Заблокирован' : 'Активен' }}
                </div>
            </div>

            <div class="card" style="margin-top: 16px;">
                <div class="muted">Здесь будет история покупок, библиотека и настройки аккаунта.</div>
            </div>
        @endauth

        @guest
            <div class="muted">Сначала нужно войти в аккаунт.</div>
            <div style="margin-top: 12px;">
                <a class="btn" href="/login">Войти</a>
            </div>
        @endguest
    </section>
</main>
@endsection
