@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/mypage_index.css') }}">

<div class="mypage-container">
    <div class="profile-section">
        <img class="user-icon"
            src="{{ $user->profile && $user->profile->image_path
            ? asset('storage/' . $user->profile->image_path)
            : asset('images/default-user.png') }}"
            alt="ユーザー画像">

        <div class="user-info">
            <div class="user-header">
                <h2 class="username">{{ $user->name }}</h2>
                <a href="{{ route('profile.update') }}" class="edit-button">プロフィールを編集</a>
            </div>

            @if ($user->average_rating)
                <div class="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($user->average_rating >= $i)
                            <span class="star full">★</span>
                        @elseif ($user->average_rating == $i - 0.5)
                            <span class="star half">★</span>
                        @else
                            <span class="star empty">☆</span>
                        @endif
                    @endfor
                </div>
            @endif
        </div>
    </div>

    <div class="tab-section">
        <button class="tab active" id="tab-sell">出品した商品</button>
        <button class="tab" id="tab-buy">購入した商品</button>
        <button class="tab" id="tab-trading">
            取引中の商品
            @if ($unreadCount > 0)
                <span class="tab-badge">{{ $unreadCount }}</span>
            @endif
        </button>
    </div>

    <div class="items-section">
        <div id="sell-items" class="item-grid">
            @forelse($items as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . ($item->itemImages->first()->image_path ?? 'images/no_image.png')) }}" alt="{{ $item->title }}">
                    <p class="item-name">{{ $item->title }}</p>
                    <p class="item-price">￥{{ number_format($item->price) }}</p>
                    <span class="item-status {{ $item->status === 'sold' ? 'sold-out' : 'on-sale' }}">
                        {{ $item->status === 'sold' ? 'Sold' : 'Sale' }}
                    </span>
                </div>
            @empty
                <p>まだ出品している商品はありません。</p>
            @endforelse
        </div>

        {{-- 購入商品 --}}
        <div id="buy-items" class="item-grid" style="display:none;">
            @forelse ($boughtItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . ($item->itemImages->first()->image_path ?? 'images/no_image.png')) }}" alt="{{ $item->title }}">
                    <p class="item-name">{{ $item->title }}</p>
                </div>
            @empty
                <p>購入した商品はありません。</p>
            @endforelse
        </div>

        {{-- 取引中の商品 --}}
        <div id="trading-items" class="item-grid" style="display:none;">
            @forelse ($tradingChats as $chat)
                <a href="{{ route('purchase_chats.show', $chat->id) }}" class="item-card">
                    <img src="{{ asset('storage/' . ($chat->item->itemImages->first()->image_path ?? 'images/no_image.png')) }}"
                        alt="{{ $chat->item->title }}">

                    <p class="item-name">{{ $chat->item->title }}</p>
                    <p class="item-price">￥{{ number_format($chat->item->price) }}</p>

                    @if ($chat->unread_count > 0)
                        <span class="badge">{{ $chat->unread_count }}</span>
                    @endif
                </a>
            @empty
                <p>取引中の商品はありません。</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    const tabSell = document.getElementById('tab-sell');
    const tabBuy = document.getElementById('tab-buy');
    const tabTrading = document.getElementById('tab-trading');
    const sellItems = document.getElementById('sell-items');
    const buyItems = document.getElementById('buy-items');
    const tradingItems = document.getElementById('trading-items');

    tabSell.addEventListener('click', () => {
        tabSell.classList.add('active');
        tabBuy.classList.remove('active');
        tabTrading.classList.remove('active');
        sellItems.style.display = 'grid';
        buyItems.style.display = 'none';
        tradingItems.style.display = 'none';
    });

    tabBuy.addEventListener('click', () => {
        tabBuy.classList.add('active');
        tabSell.classList.remove('active');
        tabTrading.classList.remove('active');
        sellItems.style.display = 'none';
        buyItems.style.display = 'grid';
        tradingItems.style.display = 'none';
    });

    tabTrading.addEventListener('click', () => {
        tabTrading.classList.add('active');
        tabSell.classList.remove('active');
        tabBuy.classList.remove('active');
        sellItems.style.display = 'none';
        buyItems.style.display = 'none';
        tradingItems.style.display = 'grid';
    });
</script>
@endsection