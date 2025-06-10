<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'item_id'        => Item::factory(),
            'postal_code'    => $this->faker->postcode(),
            'address'        => $this->faker->address(),
            'building'       => $this->faker->secondaryAddress(),
            'payment_method' => 'card', // 任意の固定値でOK
        ];
    }
}