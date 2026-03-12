@extends('layouts.app')

@section('title', 'Вход')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 520px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Вход</h2>

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2;">
                <div class="error">Проверьте данные и попробуйте снова.</div>
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Пароль</label>
                <input class="input" id="password" name="password" type="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn" type="submit">Войти</button>
        </form>

        <p class="muted" style="margin-top: 16px;">
            Нет аккаунта? <a href="/register"><strong>Зарегистрироваться</strong></a>
        </p>
    </section>
</main>
@endsection