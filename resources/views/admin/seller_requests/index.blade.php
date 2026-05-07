@extends('layouts.app')

@section('title', 'Заявки продавцов')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Заявки на статус продавца</h2>
        <p class="muted">Пока заявка не одобрена, пользователь не получает доступ к кабинету продавца и не может публиковать игры.</p>
    </section>

    @if (session('status'))
        <section class="card" style="border-color: #bfdbfe; background: #eff6ff;">
            {{ session('status') }}
        </section>
    @endif

    <section class="card">
        @if ($requests->isEmpty())
            <div class="muted">Заявок пока нет.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($requests as $sellerRequest)
                    <div class="card" style="display: grid; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: flex-start;">
                            <div>
                                <strong>{{ $sellerRequest->user->name }}</strong>
                                <div class="muted">Email: {{ $sellerRequest->user->email }}</div>
                                <div class="muted">Статус: {{ $sellerRequest->status }}</div>
                                <div class="muted">Дата: {{ $sellerRequest->created_at?->format('d.m.Y H:i') }}</div>
                            </div>
                            @if ($sellerRequest->status === 'pending')
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <form method="POST" action="{{ route('admin.seller_requests.approve', $sellerRequest) }}">
                                        @csrf
                                        <button class="btn" type="submit">Одобрить</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.seller_requests.reject', $sellerRequest) }}">
                                        @csrf
                                        <button class="pill" type="submit">Отклонить</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div style="white-space: pre-wrap;">{{ $sellerRequest->message }}</div>

                        @if ($sellerRequest->reviewer)
                            <div class="muted">Проверил: {{ $sellerRequest->reviewer->name }} {{ $sellerRequest->reviewed_at ? '· ' . $sellerRequest->reviewed_at->format('d.m.Y H:i') : '' }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection