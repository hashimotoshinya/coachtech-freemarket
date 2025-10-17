@extends('layouts.app2')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/chats_show.css') }}">
@endsection

@section('content')
<div id="review-modal" class="modal">
    <div class="modal-content">
        <h3>取引が完了しました。</h3>
        <p>今回の取引相手はどうでしたか？</p>

        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="chat_id" value="{{ $chat->id }}">
            <input type="hidden" name="reviewed_id" value="{{ $partner->id }}">
            <div class="star-rating">
                @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                    <label for="star{{ $i }}">★</label>
                @endfor
            </div>

            <button type="submit" class="submit-btn">送信する</button>
        </form>
    </div>
</div>

@if($showReviewModal)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("review-modal").style.display = "block";
    });
</script>
@endif

<div class="chat-layout">
    <aside class="chat-sidebar">
        {{-- サイドバー --}}
        <div class="chat-sidebar">
            <h3>取引一覧</h3>
            <ul class="chat-list">
                @foreach ($chats as $c)
                    @php
                        $partner = $c->buyer_id === auth()->id() ? $c->seller : $c->buyer;
                        $lastMessage = $c->messages->first();
                    @endphp
                    <li class="chat-list-item {{ $c->id === $chat->id ? 'active' : '' }}">
                        <a href="{{ route('purchase_chats.show', $c->id) }}">
                            <img src="{{ $partner->profile && $partner->profile->image_path
                                ? asset('storage/' . $partner->profile->image_path)
                                : asset('images/default-user.png') }}"
                                alt="プロフィール画像" class="profile-image">
                            <div class="chat-list-text">
                                <div class="partner-name">{{ $partner->name }}</div>
                                <div class="item-title">{{ $c->item->title }}</div>
                            </div>
                            @php
                                $unreadCount = $c->messages
                                    ->where('user_id', '!=', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="unread-badge">{{ $unreadCount }}</span>
                            @endif
                            <form action="{{ route('purchase_chats.destroy', $c->id) }}" method="POST">
                            @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('この取引を非表示にしますか？')">
                                    削除
                                </button>
                            </form>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    <div class="chat-container">
        <div class="chat-fixed-header">
            <div class="chat-header">
                {{-- 相手のプロフィール --}}
                @php
                    $partner = $chat->buyer_id === auth()->id() ? $chat->seller : $chat->buyer;
                @endphp

                <div class="chat-partner">
                    <img src="{{ $partner->profile && $partner->profile->image_path
                        ? asset('storage/' . $partner->profile->image_path)
                        : asset('images/default-user.png') }}"
                        alt="プロフィール画像" class="profile-image">
                        <span class="partner-name">
                            {{ $partner->name }}さんとの取引画面
                        </span>
                </div>
                @if ($chat->buyer_id === auth()->id() && !$chat->completed_at)
                    <form action="{{ route('purchase_chats.complete', $chat->id) }}" method="POST" id="complete-form">
                        @csrf
                        <button type="submit" class="btn btn-danger">取引を完了する</button>
                    </form>
                @endif
            </div>
            {{-- 商品情報 --}}
            <div class="item-info">
                <img src="{{ asset('storage/' . ($chat->item->itemImages->first()->image_path ?? 'images/no_image.  png')) }}" alt="商品画像" class="item-image">
                <div>
                    <h2>{{ $chat->item->title }}</h2>
                    <p>{{ number_format($chat->item->price) }}円</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div id="flash-message" class="flash-message">
                {{ session('success') }}
            </div>
        @endif

        {{-- メッセージ一覧 --}}
        <div class="chat-messages-wrapper">
            <div class="chat-messages">
                @foreach ($chat->messages as $message)
                    <div class="chat-message {{ $message->user_id === auth()->id() ? 'mine' : 'other' }}">
                        <div class="message-header">
                            <img src="{{ $partner->profile && $partner->profile->image_path
                                ? asset('storage/' . $partner->profile->image_path)
                                : asset('images/default-user.png') }}"
                                alt="プロフィール画像" class="profile-image">
                            <strong>{{ $message->user->name }}</strong>
                        </div>

                        <div class="message-body">
                            <p>{{ $message->body }}</p>
                            @if ($message->image_path)
                                <img src="{{ asset('storage/' . $message->image_path) }}" class="chat-image">
                            @endif
                        </div>

                        <div class="message-footer">
                            <span class="time">{{ $message->created_at->format('Y-m-d H:i') }}</span>
                            @if ($message->user_id === auth()->id())
                                <div class="message-actions">
                                    <a href="{{ route('messages.edit', $message->id) }}" class="action-link">編集</a>
                                    <form action="{{ route('messages.destroy', $message->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-link-delete"
                                                onclick="return confirm('本当に削除しますか？')">削除</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('purchase_chats.messages.store', $chat->id) }}"
                    method="POST" enctype="multipart/form-data"
                    class="chat-form-fixed">
                @csrf
                <textarea name="body" placeholder="取引メッセージを記入してください">{{ old('body') }}</textarea>
                @error('body')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <label for="image-upload" class="image-upload-label">画像を追加</label>
                <input type="file" id="image-upload" name="image">
                <div class="error-message" id="image-error"></div>

                <div class="preview-container" id="preview-container"></div>

                <button type="submit" class="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.classList.add('hide');
            }, 1000);
        }

        const textarea = document.querySelector("textarea[name='body']");
        if (textarea) {
            const storageKey = "chat_draft_{{ $chat->id }}";
            const saved = localStorage.getItem(storageKey);
            if (saved && !textarea.value) {
                textarea.value = saved;
            }

            textarea.addEventListener("input", function () {
                localStorage.setItem(storageKey, textarea.value);
            });

            textarea.form.addEventListener("submit", function () {
                localStorage.removeItem(storageKey);
            });
        }

        const imageUpload = document.getElementById('image-upload');
        const imageError = document.getElementById('image-error');

        if (imageUpload) {
            imageUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                imageError.textContent = "";

                if (file) {
                    const allowedTypes = ['image/jpeg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        imageError.textContent = '「.png」または「.jpeg」形式でアップロードしてください';
                        event.target.value = "";
                        return;
                    }
                    const previewContainer = document.getElementById('preview-container');
                    previewContainer.innerHTML = "";
                    const fileName = document.createElement('span');
                    fileName.classList.add('file-name');
                    fileName.textContent = file.name;
                    previewContainer.appendChild(fileName);

                    if (file.type.startsWith("image/")) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        previewContainer.appendChild(img);
                    }
                }
            });
        }

        const modal = document.getElementById("review-modal");
        const closeArea = document.querySelector(".modal");

        if (closeArea) {
            closeArea.addEventListener("click", function (e) {
                if (e.target === closeArea) {
                    modal.style.display = "none";
                }
            });
        }
    });
</script>
@endsection

