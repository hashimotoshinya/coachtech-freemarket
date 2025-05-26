<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH Logo" class="h-8">
        </div>
    </header>

    <main class="main">
        <h2 class="title">ログイン</h2>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password">
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="login-button">ログインする</button>
        </form>

        <div class="register-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </div>
    </main>
</body>
</html>