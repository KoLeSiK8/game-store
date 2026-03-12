@extends('layouts.app')

@section('title', 'Пользователи')

@section('content')
<main class="grid" style="gap: 20px;">
    @include('admin.partials.nav')
    <section class="card">
        <h2 style="margin-top: 0;">Пользователи</h2>
        <p class="muted">Управление пользователями и ролями.</p>
    </section>

    <section class="card">
        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff; margin-bottom: 12px;">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid" style="gap: 12px;">
            @foreach ($users as $user)
                <div class="card" style="display: grid; gap: 8px;">
                    <div>
                        <strong>{{ $user->name }}</strong>
                        <div class="muted">{{ $user->email }}</div>
                        <div class="muted">Роли:</div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            @if ($user->roles->isEmpty())
                                <span class="pill">—</span>
                            @else
                                @foreach ($user->roles as $role)
                                    <span class="pill">{{ $role->name }}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="muted">Статус: {{ $user->is_banned ? 'Заблокирован' : 'Активен' }}</div>
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <form method="POST" action="/admin/users/{{ $user->id }}/ban">
                            @csrf
                            <button class="pill" type="submit">
                                {{ $user->is_banned ? 'Разблокировать' : 'Заблокировать' }}
                            </button>
                        </form>
                        <form method="POST" action="/admin/users/{{ $user->id }}/role" style="display: flex; gap: 8px; align-items: center;">
                            @csrf
                            <select name="role" class="input" style="padding: 8px 10px;">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button class="pill" type="submit">Назначить</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 16px;">
            {{ $users->links() }}
        </div>
    </section>
</main>
@endsection
