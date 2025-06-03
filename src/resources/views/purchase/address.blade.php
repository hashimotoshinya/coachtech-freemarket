@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-container">
    <h2>住所の変更</h2>

    <form action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}" placeholder="例：123-4567">
            @error('postal_code')
                <div class="field-error">※ {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}">
            @error('address')
                <div class="field-error">※ {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building ?? '') }}">
            @error('building_name')
                <div class="field-error">※ {{ $message }}</div>
            @enderror
        </div>

        <div class="form-button">
            <button type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection