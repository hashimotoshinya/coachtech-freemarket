@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/sells_create.css') }}">

<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        <div class="form-group">
            <label for="image">商品画像</label>
            <input type="file" name="image" id="image" accept="image/*">
            @error('image')
                <div class="error">{{ $message }}</div>
            @enderror
            {{-- プレビュー画像 --}}
            <div id="preview" style="margin-top: 10px;">
                <img id="previewImage" src="#" alt="画像プレビュー" style="max-width: 300px; display: none;" />
            </div>
        </div>

        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-tags">
                @foreach($categories as $category)
                    <label class="tag">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                            {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'checked' : '' }}>
                        <span>{{ $category->name }}</span> {{-- ← 表示は名前のまま --}}
                    </label>
                @endforeach
            </div>
            @error('categories')
                <div class="error">{{ $message }}</div>
            @enderror
            @error('categories.*')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition">
                <option value="">選択してください</option>
                @foreach (['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'] as $condition)
                    <option value="{{ $condition }}" {{ old('condition') === $condition ? 'selected' : '' }}>
                        {{ $condition }}
                    </option>
                @endforeach
            </select>
            @error('condition')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="title">商品名</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}">
            @error('title')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
            @error('brand')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">商品の説明</label>
            <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="price">販売価格</label>
            <div class="price-input">
                <span>¥</span>
                <input type="number" name="price" id="price" min="0" value="{{ old('price') }}">
            </div>
            @error('price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>

<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewImage = document.getElementById('previewImage');

    previewImage.style.display = 'none';
    previewImage.src = '#';

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection