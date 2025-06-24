@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">

    <div class="item-container">
        <div class="item-image-wrapper">
            @if ($item->itemImages->isNotEmpty())
                <img src="{{ asset('storage/' . $item->itemImages->first()->image_path) }}"
                    alt="商品画像"
                    class="item-image">
            @else
                <div class="item-image no-image">
                    商品画像なし
                </div>
            @endif
        </div>

        <div class="item-details">
            <h1 class="item-title">{{ $item->title }}</h1>
            <p class="item-brand">{{ $item->brand }}</p>
            <p class="item-price">
                ¥{{ number_format($item->price) }} <span class="item-tax">(税込)</span>
            </p>
            <div class="item-meta">
                <form method="POST" action="{{ auth()->user() && auth()->user()->favorites->contains($item->id) ? route('favorite.destroy', $item) : route('favorite.store', $item) }}">
                    @csrf
                    @if (auth()->user() && auth()->user()->favorites->contains($item->id))
                        @method('DELETE')
                        <button type="submit" class="favorite-button liked">⭐️</button>
                    @else
                        <button type="submit" class="favorite-button">⭐︎</button>
                    @endif
                </form>
                <span>{{ $item->favoredByUsers->count() }}</span>

                <span class="comment-count">💬 {{ $item->comments->count() }}</span>
            </div>

            @auth
                <a href="{{ route('items.purchase', $item->id) }}" class="purchase-button">
                    購入手続きへ
                </a>
            @else
                <a href="{{ route('login') }}" class="purchase-button">
                    購入手続きへ（ログイン）
                </a>
            @endauth

            <div class="section">
                <h2 class="section-title">商品説明</h2>
                <p class="section-text">{{ $item->description }}</p>
            </div>

            <div class="section">
                <h2 class="section-title">商品情報</h2>
                <p>
                    <strong>カテゴリ：</strong>
                    @if ($item->categories && $item->categories->count())
                        @foreach ($item->categories as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    @else
                        <span class="category-none">カテゴリなし</span>
                    @endif
                </p>
                <p><strong>商品の状態：</strong> {{ $item->condition }}</p>
            </div>

            <div class="comments">
                <h2 class="comments-title">コメント ({{ $item->comments->count() }})</h2>
                <div class="comment-list">
                    @foreach ($item->comments as $comment)
                        <div class="comment">
                            <div class="comment-avatar">
                                <img class="comment-avatar"
                                    src="{{ $user->profile && $user->profile->image_path
                                    ? asset('storage/' . $user->profile->image_path)
                                    : asset('images/default-user.png') }}"
                                    alt="ユーザー画像">
                            </div>
                            <div>
                                <p class="comment-user">{{ $comment->user->name }}</p>
                                <div class="comment-body">{{ $comment->content }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="comment-form-wrapper">
                    @auth
                        <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form">
                            @csrf
                            <label for="content">商品へのコメント</label>
                            <textarea name="content" id="content" rows="3">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="error">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="purchase-button">コメントを送信する</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="purchase-button">
                            コメントする（ログイン）
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </div>

@endsection