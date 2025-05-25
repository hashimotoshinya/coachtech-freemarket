@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/items_index.css') }}">
@section('content')
<div class="tab-container">
    <div class="tab-menu">
        <button class="tab active" data-tab="recommended">おすすめ</button>
        <button class="tab" data-tab="mylist" onclick="redirectIfGuest()">マイリスト</button>
    </div>

    <div class="tab-content">
        {{-- おすすめ商品 --}}
        <div id="recommended" class="tab-pane active">
        {{-- 検索ワードがある場合に検索結果の文言を表示 --}}
            @if(request('keyword'))
                <p class="text-sm text-gray-600 mb-4">
                    <strong>{{ request('keyword') }}</strong>」の検索結果（{{ $items->total() }}件）
                </p>
            @endif
            <div class="item-grid">
                @foreach($items as $item)
                    <a href="{{ route('items.show', $item->id) }}" class="item-card">
                    <img src="{{ asset('storage/' . ($item->itemImages->first()->image_path ?? 'images/no_image.png')) }}" alt="{{ $item->name }}">
                        <p class="item-name">{{ $item->title }}</p>
                        <p class="item-price">￥{{ number_format($item->price) }}</p>
                        <span class="item-status {{ $item->is_sold ? 'sold-out' : 'on-sale' }}">
                            {{ $item->is_sold ? '売り切れ' : '出品中' }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- マイリスト --}}
        <div id="mylist" class="tab-pane" style="display:none;">
            @auth
                @if(request('keyword'))
                    <p class="text-sm text-gray-600 mb-4">
                        <strong>{{ request('keyword') }}</strong>」のマイリスト検索結果（{{ $myItems->count() }}件）
                    </p>
                @endif
                <div class="item-grid">
                    @forelse($myItems as $item)
                        <a href="{{ route('items.show', $item->id) }}" class="item-card">
                        <img src="{{ asset('storage/' . ($item->itemImages->first()->image_path ?? 'images/no_image.png')) }}" alt="商品画像">
                            <p class="item-name">{{ $item->name }}</p>
                            <span class="item-status {{ $item->is_sold ? 'sold-out' : 'on-sale' }}">
                                {{ $item->is_sold ? '売り切れ' : '出品中' }}
                            </span>
                        </a>
                    @empty
                        <p>マイリストに商品がありません。</p>
                    @endforelse
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const tabs = document.querySelectorAll('.tab');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            panes.forEach(pane => {
                pane.style.display = pane.id === target ? 'block' : 'none';
            });
        });
    });

    function redirectIfGuest() {
        @guest
            window.location.href = "{{ route('login') }}";
        @endguest
    }
</script>
@endsection