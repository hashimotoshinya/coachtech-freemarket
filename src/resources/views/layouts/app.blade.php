<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH フリマ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @yield('css')
</head>
<body>

    <header>
        <div class="header-logo">
            <a href="{{ route('items.index') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH Logo">
            </a>
        </div>

        <form action="{{ url()->current() }}" method="GET">
            <input
                type="text"
                name="keyword"
                placeholder="なにをお探しですか？"
                value="{{ request('keyword') }}"
            >
        </form>

        <div>
            @auth
                <form  action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}">ログイン</a>
            @endauth
            <a href="{{ auth()->check() ? route('mypage.index') : route('login') }}">マイページ</a>
            <a href="{{ auth()->check() ? route('sell.create') : route('login') }}">
                <button>出品</button>
            </a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>