<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;

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

        // 購入記録を保存
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => $addressData['postal_code'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
            'payment_method' => $request->input('payment_method'),
        ]);

        // セッションから削除
        session()->forget('purchase_address');

        return redirect()->route('purchase.thanks')->with('message', '購入が完了しました。');
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