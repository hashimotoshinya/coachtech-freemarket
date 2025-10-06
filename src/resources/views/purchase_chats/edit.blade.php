@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase_chats_edit.css') }}">
@endsection

@section('content')
<div class="container edit-message-container">
    <h2>メッセージを編集</h2>

    <form action="{{ route('messages.update', $message->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="body">本文</label>
        <textarea id="body" name="body">{{ old('body', $message->body) }}</textarea>
        @error('body')
            <div class="error-message">{{ $message }}</div>
        @enderror

        <div>
            @if ($message->image_path)
                <p>現在の画像:</p>
                <img src="{{ asset('storage/' . $message->image_path) }}" width="200">
            @endif
        </div>

        <label for="image">画像を変更（任意）</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png">
        @error('image')
            <div class="error-message">{{ $message }}</div>
        @enderror

        <button type="submit">更新する</button>
    </form>
</div>
@endsection