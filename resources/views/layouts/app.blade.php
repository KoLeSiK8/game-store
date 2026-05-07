<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Game Store')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,600,700|sora:500,600,700" rel="stylesheet" />

    <style>
        :root {
            --bg: #f3f6fb;
            --ink: #0f172a;
            --muted: #475569;
            --primary: #2563eb;
            --primary-2: #0ea5e9;
            --card: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, Segoe UI, sans-serif;
            color: var(--ink);
            background: radial-gradient(1200px 600px at 20% -10%, #dbeafe 0%, transparent 60%),
                        radial-gradient(900px 500px at 110% 10%, #e0f2fe 0%, transparent 55%),
                        var(--bg);
        }

        a { color: inherit; text-decoration: none; }
        .container { width: min(1100px, 92vw); margin: 0 auto; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
            gap: 16px;
        }
        .brand { display: flex; gap: 12px; align-items: center; }
        .logo {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: var(--shadow);
        }
        .brand h1 {
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            letter-spacing: 0.4px;
            margin: 0;
        }
        .nav { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; justify-content: flex-end; }
        .pill {
            border: 1px solid var(--border);
            padding: 8px 14px;
            border-radius: 999px;
            background: #fff;
            font-weight: 600;
            font-size: 14px;
        }
        .pill.primary { background: var(--primary); color: #fff; border-color: var(--primary); }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--shadow);
        }

        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        @media (max-width: 900px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }
        .btn.secondary { background: #0f172a; }
        .muted { color: var(--muted); }
        .field { display: grid; gap: 6px; margin-bottom: 14px; }
        .input {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fff;
            font-size: 15px;
        }
        .error { color: #b91c1c; font-size: 14px; }
        footer { padding: 40px 0 60px; color: var(--muted); font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <header class="topbar">
            <a class="brand" href="{{ url('/') }}">
                <span class="logo"></span>
                <h1>Game Store</h1>
            </a>
            <nav class="nav">
                <a class="pill" href="{{ url('/') }}">Главная</a>
                @auth
                    @php
                        $roleNames = auth()->user()->roles->pluck('name');
                    @endphp
                    <a class="pill" href="{{ route('profile') }}">Профиль</a>
                    <a class="pill" href="{{ route('library.index') }}">Моя библиотека</a>
                    <a class="pill" href="{{ route('wishlist.list') }}">Избранное</a>
                    <a class="pill" href="{{ route('recommendations.index') }}">Рекомендации</a>
                    <a class="pill" href="{{ route('cart.view') }}">Корзина</a>
                    @if ($roleNames->contains('seller'))
                        <a class="pill" href="{{ route('seller.dashboard') }}">Кабинет продавца</a>
                    @endif
                    @if ($roleNames->contains('admin'))
                        <a class="pill" href="{{ route('admin.index') }}" target="_blank" rel="noopener noreferrer">Админ: панель</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="pill" type="submit">Выйти</button>
                    </form>
                @endauth
                @guest
                    <a class="pill" href="{{ route('login') }}">Войти</a>
                    <a class="pill primary" href="{{ route('register') }}">Регистрация</a>
                @endguest
            </nav>
        </header>

        @yield('content')

        <footer>
            Game Store
        </footer>
    </div>
</body>
</html>