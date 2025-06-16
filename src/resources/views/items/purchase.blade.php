@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.complete', ['item_id' => $item->id]) }}" method="POST">
    @csrf
    <div class="purchase-wrapper">
        <div class="left-column">
            <div class="item-image">
                @if ($item->itemImages->isNotEmpty())
                    <img src="{{ asset('storage/' . $item->itemImages->first()->image_path) }}" alt="商品画像">
                @else
                    <div class="no-image">商品画像なし</div>
                @endif
            </div>

            <div class="item-details">
                <h2 class="item-name">{{ $item->title }}</h2>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>

            <div class="payment-section">
                <h3>支払い方法</h3>
                <div class="select-box">
                    <select name="payment_method" onchange="updatePaymentMethod(this)">
                        <option value="">選択してください</option>
                        <option value="convenience" {{ old('payment_method') == 'convenience' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>カード払い</option>
                    </select>
                    @if ($errors->has('payment_method'))
                        <p class="error">{{ $errors->first('payment_method') }}</p>
                    @endif
                </div>
            </div>

            <div class="shipping-section">
                <h3>配送先</h3>
                @if ($sessionAddress ?? '')
                    <p>
                        〒{{ $sessionAddress['postal_code'] ?? '' }}<br>
                        {{ $sessionAddress['address'] ?? '' }}<br>
                        {{ $sessionAddress['building'] ?? '' }}
                    </p>
                @elseif ($profile)
                    <p>
                        〒{{ $profile->postal_code }}<br>
                        {{ $profile->address }}<br>
                        {{ $profile->building }}
                    </p>
                @else
                    <p>
                        〒 XXX-YYYY<br>
                        ここには住所と建物が入ります
                    </p>
                @endif

                @if ($errors->has('profile'))
                    <p class="error">{{ $errors->first('profile') }}</p>
                @endif

                <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="address-edit-link">変更する</a>
            </div>
        </div>

        <div class="right-column">
            <div class="summary-box">
                <table>
                    <tr>
                        <td>商品代金</td>
                        <td class="text-right">¥{{ number_format($item->price) }}</td>
                    </tr>
                    <tr>
                        <td>支払い方法</td>
                        <td class="text-right" id="selected-method">
                            {{ old('payment_method') ?: '未選択' }}
                        </td>
                    </tr>
                </table>
            </div>

            <div class="purchase-button-container">
                <button type="submit" class="purchase-button">購入する</button>
            </div>
        </div>
    </div>
</form>

<script>
    function updatePaymentMethod(select) {
        const selected = select.options[select.selectedIndex].text;
        document.getElementById('selected-method').innerText = selected || '未選択';
    }
</script>
@endsection