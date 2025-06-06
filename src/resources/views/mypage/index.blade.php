@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/mypage_index.css') }}">

<div class="mypage-container">
    <div class="profile-section">
    <img class="user-icon"
        src="{{ $user && $user->image_path ? asset('storage/' . $user->image_path) : asset('images/default-user.png') }}"
        alt="ユーザー画像">
        <h2 class="username">{{ $user->name }}</h2>
        <a href="{{ route('profile.update') }}" class="edit-button">プロフィールを編集</a>
    </div>

    <div class="tab-section">
        <button class="tab active" id="tab-sell">出品した商品</button>
        <button class="tab" id="tab-buy">購入した商品</button>
    </div>

    <div class="items-section">
        <div id="sell-items" class="item-grid">
            @forelse($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="item-card">
                    <img src="{{ asset('storage/' . ($item->itemImages->first()->image_path ?? 'images/no_image.png')) }}" alt="{{ $item->title }}">
                    <p class="item-name">{{ $item->title }}</p>
                    <p class="item-price">￥{{ number_format($item->price) }}</p>
                    <span class="item-status {{ $item->is_sold ? 'sold-out' : 'on-sale' }}">
                        {{ $item->is_sold ? '売り切れ' : '出品中' }}
                    </span>
                </a>
            @empty
                <p>まだ出品している商品はありません。</p>
            @endforelse
        </div>

        <div id="buy-items" class="item-list" style="display:none;">
            @forelse ($boughtItems as $item)
                <div class="item-card">
                <img src="{{ asset('storage/' . ($item->itemImages->first()->image_path ?? 'images/no_image.png')) }}" alt="{{ $item->title }}">
                    <p class="item-name">{{ $item->title }}</p>
                </div>
            @empty
                <p>購入した商品はありません。</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    const tabSell = document.getElementById('tab-sell');
    const tabBuy = document.getElementById('tab-buy');
    const sellItems = document.getElementById('sell-items');
    const buyItems = document.getElementById('buy-items');

    tabSell.addEventListener('click', () => {
        tabSell.classList.add('active');
        tabBuy.classList.remove('active');
        sellItems.style.display = 'block';
        buyItems.style.display = 'none';
    });

    tabBuy.addEventListener('click', () => {
        tabBuy.classList.add('active');
        tabSell.classList.remove('active');
        sellItems.style.display = 'none';
        buyItems.style.display = 'block';
    });
</script>
@endsection