@extends('layouts.app')

@section('title', 'Отзыв')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 700px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Оставить отзыв</h2>
        <p class="muted">Игра: <strong>{{ $game->title }}</strong></p>

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2;">
                <div class="error">Проверьте заполнение полей.</div>
            </div>
        @endif

        <form method="POST" action="/games/{{ $game->id }}/reviews">
            @csrf
            <div class="field">
                <label for="rating">Оценка</label>
                <select id="rating" name="rating" class="input" required>
                    @foreach ([1, 2, 3, 4, 5] as $rating)
                        <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }}</option>
                    @endforeach
                </select>
                @error('rating')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="content">Комментарий</label>
                <textarea class="input" id="content" name="content" rows="4">{{ old('content') }}</textarea>
                @error('content')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn" type="submit">Отправить</button>
        </form>
    </section>
</main>
@endsection