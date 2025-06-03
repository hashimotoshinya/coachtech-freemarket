<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class PurchaseController extends Controller
{
    // 購入完了処理
    public function complete(PurchaseRequest $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);
        $profile = $user->profile;

        if (!$profile) {
            return back()->withErrors([
                'profile' => '配送先住所を入力してください。',
            ])->withInput();
        }

        // 住所：セッションに変更済み住所があればそれを使う、なければプロフィールから取得
        $addressData = session('purchase_address', [
            'postal_code' => $profile->postal_code,
            'address' => $profile->address,
            'building' => $profile->building,
        ]);

        // 🔽 支払い方法によって処理を分岐
        if ($request->payment_method === 'card') {
            Stripe::setApiKey(config('services.stripe.secret')); // config/services.php に設定必要

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price * 100, // 円 → センチ
                        'product_data' => [
                            'name' => $item->title,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('items.index'),
                'cancel_url' => route('items.purchase', ['item' => $item->id]),
            ]);

            return redirect($session->url);
        } elseif ($request->payment_method === 'convenience') {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $item->price * 100,
                'currency' => 'jpy',
                'payment_method_types' => ['konbini'],
                'description' => $item->title,
                'metadata' => [
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                ],
            ]);

            // 本来はウェブフックで支払い確定を確認するが、ここでは一旦画面表示で仮対応
            return view('purchase.konbini', [
                'paymentIntent' => $paymentIntent,
                'item' => $item,
            ]);
        }

        return back()->withErrors(['payment_method' => '支払い方法が正しくありません。']);
    }
    //Stripe成功時のコールバック
    public function stripeSuccess($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);
        $addressData = session('purchase_address');

        // 決済完了後、購入レコードを保存
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => $addressData['postal_code'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
            'payment_method' => 'card',
        ]);

        session()->forget('purchase_address');

        return redirect()->route('purchase.thanks')->with('message', 'カードでの購入が完了しました。');
    }

    // 住所変更フォーム表示
    public function editAddress($item_id)
    {
        $user = auth()->user();
        return view('purchase.address', compact('user', 'item_id'));
    }

    public function showPurchaseForm($item_id)
    {
        $item = Item::with('itemImages')->findOrFail($item_id);
        $user = auth()->user();
        $profile = $user->profile;

        $sessionAddress = session('purchase_address'); // ← これを渡す

        return view('items.purchase', compact('item', 'profile', 'sessionAddress'));
    }

    // 一時的に住所を変更（セッションに保存）
    public function updateAddress(AddressRequest $request, $item_id)
    {
        // セッションに一時保存（DBのuser/profile情報は更新しない）
        session([
            'purchase_address' => [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        ]);

        return redirect()->route('items.purchase', ['item' => $item_id])
                        ->with('success', '配送先住所を変更しました。');
    }
}