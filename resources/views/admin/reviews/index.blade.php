@extends('layouts.app')

@section('title', 'Модерация отзывов')

@section('content')
<main class="grid" style="gap: 20px;">
    @include('admin.partials.nav')
    <section class="card">
        <h2 style="margin-top: 0;">Модерация отзывов</h2>
        <p class="muted">Отзывы на рассмотрении.</p>
    </section>

    <section class="card">
        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff; margin-bottom: 12px;">
                {{ session('status') }}
            </div>
        @endif

        @if ($reviews->isEmpty())
            <div class="muted">Нет отзывов на модерации.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($reviews as $review)
                    <div class="card" style="display: grid; gap: 8px;">
                        <div>
                            <strong>{{ $review->game->title }}</strong>
                            <div class="muted">Пользователь: {{ $review->user->email }}</div>
                            <div class="muted">Оценка: {{ $review->rating }}</div>
                        </div>
                        <div class="muted">{{ $review->content ?? 'Без комментария.' }}</div>
                        <div style="display: flex; gap: 8px;">
                            <form method="POST" action="/admin/reviews/{{ $review->id }}/approve">
                                @csrf
                                <button class="pill" type="submit">Одобрить</button>
                            </form>
                            <form method="POST" action="/admin/reviews/{{ $review->id }}">
                                @csrf
                                @method('DELETE')
                                <button class="pill" type="submit">Удалить</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px;">
                {{ $reviews->links() }}
            </div>
        @endif
    </section>
</main>
@endsection
