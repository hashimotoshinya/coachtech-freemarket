<?php

namespace Database\Factories;

use App\Models\PurchaseChat;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseChatFactory extends Factory
{
    protected $model = PurchaseChat::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'completed_at' => null,
            'deleted_by_buyer' => false,
            'deleted_by_seller' => false,
        ];
    }
}