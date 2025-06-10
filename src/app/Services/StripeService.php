<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession($item)
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
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
    }

    public function createKonbiniPaymentIntent($item, $user)
    {
        return PaymentIntent::create([
            'amount' => $item->price,
            'currency' => 'jpy',
            'payment_method_types' => ['konbini'],
            'description' => $item->title,
            'metadata' => [
                'user_id' => $user->id,
                'item_id' => $item->id,
            ],
        ]);
    }
}