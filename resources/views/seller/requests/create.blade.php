@extends('layouts.app')

@section('title', 'Заявка на статус продавца')

@section('content')
<main class="grid" style="gap: 20px; max-width: 860px; margin: 0 auto;">
    <section class="card">
        <h2 style="margin-top: 0;">Стать продавцом</h2>
        <p class="muted">Заполните короткую анкету. После этого администратор проверит заявку и только после одобрения откроет вам кабинет продавца.</p>
    </section>

    @if (session('status'))
        <section class="card" style="border-color: #bfdbfe; background: #eff6ff;">
            {{ session('status') }}
        </section>
    @endif

    @if ($errors->any())
        <section class="card" style="border-color: #fecaca; background: #fef2f2;">
            {{ $errors->first() }}
        </section>
    @endif

    @if ($latestRequest)
        <section class="card">
            <h3 style="margin-top: 0;">Последняя заявка</h3>
            <div class="muted">Статус: <strong>{{ $latestRequest->status }}</strong></div>
            <div style="margin-top: 10px; white-space: pre-wrap;">{{ $latestRequest->message }}</div>
            @if ($latestRequest->status === 'pending')
                <div class="muted" style="margin-top: 10px;">Заявка уже отправлена и ожидает решения администратора.</div>
            @endif
        </section>
    @endif

    @if (!$latestRequest || $latestRequest->status !== 'pending')
        <section class="card">
            <form method="POST" action="{{ route('seller.request.submit') }}">
                @csrf
                <div class="field">
                    <label for="message">Расскажите о себе как о продавце</label>
                    <textarea class="input" id="message" name="message" rows="8" placeholder="Опишите, какие игры вы планируете публиковать, ваш опыт и почему вам нужен кабинет продавца." required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn" type="submit">Отправить заявку</button>
            </form>
        </section>
    @endif
</main>
@endsection