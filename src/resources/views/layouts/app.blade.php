<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH フリマ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
</head>
<body class="bg-gray-100">

    <header class="bg-black text-white px-6 py-4 flex items-center justify-between">
        {{-- ロゴ --}}
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH Logo" class="h-8">
        </div>

        {{-- 検索ボックス --}}
        <form action="{{ url()->current() }}" method="GET" class="flex-1 mx-6">
            <input
                type="text"
                name="keyword"
                placeholder="なにをお探しですか？"
                value="{{ request('keyword') }}"
                class="w-full rounded-md px-4 py-2 text-black"
            />
        </form>

        {{-- ナビゲーションボタン --}}
        <div class="flex items-center space-x-4">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:underline">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:underline">ログイン</a>
                <a href="{{ route('login') }}" class="hover:underline">マイページ</a>
                <a href="{{ route('login') }}">
                    <button class="bg-white text-black px-4 py-1 rounded-md">出品</button>
                </a>
            @endauth
        </div>
    </header>

    {{-- コンテンツ表示 --}}
    <main class="p-6">
        @yield('content')
    </main>

</body>
</html>