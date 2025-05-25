<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH フリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
</head>
<body class="bg-white text-gray-900 leading-relaxed">

    {{-- 商品情報 --}}
    <div class="item-container">

        {{-- 商品画像 --}}
        <div class="item-image-wrapper rounded shadow">
            @if ($item->itemImages->isNotEmpty())
                    <img src="{{ asset('storage/' . $item->itemImages->first()->image_path) }}"
                    alt="商品画像"
                    class="item-image rounded">
            @else
                <div class="flex items-center justify-center h-full text-gray-600 item-image">
                    商品画像なし
                </div>
            @endif
        </div>

        {{-- 商品詳細 --}}
        <div class="item-details space-y-4">

            <h1 class="item-title text-gray-900">{{ $item->title }}</h1>
            <p class="text-sm text-gray-600">{{ $item->brand }}</p>
            <p class="item-price text-red-500 font-semibold">
                ¥{{ number_format($item->price) }} <span class="text-sm text-gray-600">(税込)</span>
            </p>

            {{-- アイコン --}}
            <div class="flex gap-4">
                <div class="flex items-center gap-1 text-gray-700">
                    <span>⭐</span><span>{{ $item->favoredByUsers->count() }}</span>
                </div>
                <div class="flex items-center gap-1 text-gray-700">
                    <span>💬</span><span>{{ $item->comments->count() }}</span>
                </div>
            </div>

            {{-- 購入ボタン --}}
            @auth
                @if(auth()->user()->is_approved)
                    <a href="{{ route('items.purchase', $item->id) }}"
                        class="purchase-button hover:bg-red-600 transition">
                        購入手続きへ
                    </a>
                @else
                    <p class="text-red-600 font-semibold">※購入には承認が必要です。</p>
                @endif
            @else
                <a href="{{ route('login') }}"
                    class="purchase-button hover:bg-red-600 transition">
                    購入手続きへ（ログイン）
                </a>
            @endauth

            {{-- 商品説明 --}}
            <div class="section">
                <h2 class="text-lg font-semibold mb-1">商品説明</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $item->description }}</p>
            </div>

            {{-- 商品情報 --}}
            <div class="section">
                <h2 class="text-lg font-semibold mb-1">商品情報</h2>
                <p>
                    <strong>カテゴリ：</strong>
                    @if ($item->category)
                        <span class="category-tag">{{ $item->category->name }}</span>
                    @else
                        <span class="text-gray-500">カテゴリなし</span>
                    @endif
                </p>
                <p><strong>商品の状態：</strong> {{ $item->condition }}</p>
            </div>
            {{-- コメントセクション --}}
            <div class="comments">

                <h2 class="text-xl font-bold mb-4">コメント ({{ $item->comments->count() }})</h2>

                <div class="overflow-y-auto max-h-60 pr-2 space-y-4">
                    @foreach ($item->comments as $comment)
                        <div class="comment">
                            <div class="comment-avatar"></div>
                            <div>
                                <p class="font-semibold">{{ $comment->user->name }}</p>
                                <div class="comment-body">
                                    {{ $comment->content }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- コメント投稿 --}}
                @auth
                    <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form mt-6">
                        @csrf
                        <label for="content" class="block font-semibold mb-1">商品へのコメント</label>
                        <textarea name="content" id="content" rows="3" required></textarea>
                        <button type="submit">コメントを送信する</button>
                    </form>
                @else
                    <p class="text-gray-600 mt-4 text-center">
                        コメントするには <a href="{{ route('login') }}" class="text-blue-500 underline">ログイン</a> してください。
                    </p>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>