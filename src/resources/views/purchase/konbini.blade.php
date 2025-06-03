@extends('layouts.app')

@section('content')
    <div class="konbini-payment">
        <h1>コンビニ支払い情報</h1>
        <p>下記情報を使ってコンビニでお支払いください。</p>

        @if (isset($paymentIntent->next_action) && isset($paymentIntent->next_action->konbini_display_details))
            <ul>
                <li>支払い金額：{{ number_format($item->price) }}円</li>
                <li>支払い期限：{{ \Carbon\Carbon::parse($paymentIntent->next_action->konbini_display_details->expires_at)->format('Y年m月d日 H:i') }}</li>
                <li>バーコード：{{ $paymentIntent->next_action->konbini_display_details->hosted_voucher_url ?? '後ほど確認可能です' }}</li>
            </ul>
        @else
            <p>支払い詳細がまだ生成されていません。数秒後にStripeダッシュボードなどで確認できます。</p>
        @endif

        <a href="{{ route('mypage.index') }}">トップページへ</a>
    </div>
@endsection