@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 520px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Регистрация</h2>

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2;">
                <div class="error">Проверьте данные формы.</div>
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf
            <div class="field">
                <label for="name">Имя</label>
                <input class="input" id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

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

            <div class="field">
                <label for="password_confirmation">Повторите пароль</label>
                <input class="input" id="password_confirmation" name="password_confirmation" type="password" required>
            </div>

            <button class="btn" type="submit">Создать аккаунт</button>
        </form>

        <p class="muted" style="margin-top: 16px;">
            Уже есть аккаунт? <a href="/login"><strong>Войти</strong></a>
        </p>
    </section>
</main>
@endsection