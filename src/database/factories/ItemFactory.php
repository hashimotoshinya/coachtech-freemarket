<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'brand' => $this->faker->word,
            'description' => $this->faker->sentence,
            'condition' => 'new',
            'price' => $this->faker->numberBetween(1000, 10000),
            'status' => $this->faker->randomElement(['available', 'sold']),
            'user_id' => User::factory(),
        ];
    }
}