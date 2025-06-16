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
use App\Services\StripeService;

class PurchaseController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function complete(PurchaseRequest $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        $sessionAddress = session('purchase_address');
        if ($sessionAddress) {
            $postalCode = $sessionAddress['postal_code'] ?? null;
            $address = $sessionAddress['address'] ?? null;
            $building = $sessionAddress['building'] ?? null;
        } else {
            $profile = $user->profile;
            $postalCode = $profile->postal_code ?? null;
            $address = $profile->address ?? null;
            $building = $profile->building ?? null;
        }

        if (!$postalCode || !$address || !$building) {
            return back()->withErrors([
                'profile' => '配送先住所を入力してください。',
            ])->withInput();
        }

        $paymentMethod = $request->payment_method;

        $purchase = $user->purchases()->create([
            'item_id' => $item->id,
            'postal_code' => $postalCode,
            'address' => $address,
            'building' => $building,
            'payment_method' => $paymentMethod,
        ]);

        if ($paymentMethod === 'card') {
            $session = $this->stripe->createCheckoutSession($item);
            return redirect($session->url);
        } elseif ($paymentMethod === 'convenience') {
            $paymentIntent = $this->stripe->createKonbiniPaymentIntent($item, $user);

            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'postal_code' => $postalCode,
                'address' => $address,
                'building' => $building,
                'payment_method' => 'convenience',
            ]);

            $item->status = 'sold';
            $item->save();

            return view('purchase.konbini', [
                'paymentIntent' => $paymentIntent,
                'item' => $item,
            ]);
        }

        return back()->withErrors(['payment_method' => '支払い方法が正しくありません。']);
    }

    public function stripeSuccess($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);
        $addressData = session('purchase_address');

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => $addressData['postal_code'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
            'payment_method' => 'card',
        ]);

        $item->status = 'sold';
        $item->save();

        session()->forget('purchase_address');

        return redirect()->route('mypage.index')->with('message', 'カードでの購入が完了しました。');
    }

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

        $sessionAddress = session('purchase_address');

        return view('items.purchase', compact('item', 'profile', 'sessionAddress'));
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
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