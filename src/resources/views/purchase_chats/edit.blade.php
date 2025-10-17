@extends('layouts.app2')

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
        <input type="file" id="image" name="image">
        <div class="error-message" id="image-error"></div>
        <div id="preview-container"></div>
        @error('image')
            <div class="error-message">{{ $message }}</div>
        @enderror

        <button type="submit">更新する</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById('image');
    const imageError = document.getElementById('image-error');
    const previewContainer = document.getElementById('preview-container');
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            imageError.textContent = "";
            previewContainer.innerHTML = "";
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    imageError.textContent = '「.png」または「.jpeg」形式でアップロードしてください';
                    event.target.value = "";
                    return;
                }
                const fileName = document.createElement('span');
                fileName.classList.add('file-name');
                fileName.textContent = file.name;
                previewContainer.appendChild(fileName);

                if (file.type.startsWith("image/")) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = "200px";
                    img.style.marginTop = "10px";
                    previewContainer.appendChild(img);
                }
            }
        });
    }
});
</script>
@endsection