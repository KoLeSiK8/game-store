@extends('layouts.app')

@section('title', 'Модерация игр')

@section('content')
<main class="grid" style="gap: 20px;">
    @include('admin.partials.nav')
    <section class="card">
        <h2 style="margin-top: 0;">Модерация игр</h2>
        <p class="muted">Список игр, ожидающих одобрения.</p>
    </section>

    <section class="card">
        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff; margin-bottom: 12px;">
                {{ session('status') }}
            </div>
        @endif

        @if ($games->isEmpty())
            <div class="muted">Нет игр на модерации.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($games as $game)
                    <div class="card" style="display: grid; gap: 8px;">
                        <div>
                            <strong>{{ $game->title }}</strong>
                            <div class="muted">Продавец: {{ $game->seller_id }}</div>
                        </div>
                        <div class="muted">{{ $game->description ? \Illuminate\Support\Str::limit($game->description, 120) : 'Описание отсутствует.' }}</div>
                        <div style="display: flex; gap: 8px;">
                            <form method="POST" action="/admin/games/{{ $game->id }}/approve">
                                @csrf
                                <button class="pill" type="submit">Одобрить</button>
                            </form>
                            <form method="POST" action="/admin/games/{{ $game->id }}/reject">
                                @csrf
                                <button class="pill" type="submit">Отклонить</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px;">
                {{ $games->links() }}
            </div>
        @endif
    </section>
</main>
@endsection
