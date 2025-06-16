@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<div class="profile-container">
    <h2>プロフィール設定</h2>

    <form action="{{ $profileExists ? route('profile.update') : route('profile.store') }}"
            method="POST" enctype="multipart/form-data">
        @csrf
        @if ($profileExists)
            @method('PUT')
        @endif

        <div class="image-upload">
            <img id="preview-image"
                src="{{ $profile && $profile->image_path ? asset('storage/' . $profile->image_path) : asset('images/default-user.png') }}"
                class="profile-img" alt="Profile Image">

            <label class="image-select-btn">
                画像を選択する
                <input type="file" name="image" id="image-input" hidden>
            </label>
            @error('image')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
            @error('postal_code')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $profile->building ?? '') }}">
            @error('building')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-button">
            <button type="submit">{{ $profileExists ? '更新する' : '登録する' }}</button>
        </div>
    </form>
</div>

{{-- JavaScriptで画像プレビュー --}}
<script>
    document.getElementById('image-input').addEventListener('change', function (event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('preview-image').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection