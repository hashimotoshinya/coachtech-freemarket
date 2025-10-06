@component('mail::message')
# 取引完了のお知らせ

取引が完了しました。<br>
購入者: {{ $chat->buyer->name }}<br>
商品: {{ $chat->item->name }}

今後の取引の参考に、評価をお願いいたします。

@endcomponent