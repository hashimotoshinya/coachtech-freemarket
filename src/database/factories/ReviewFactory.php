<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\PurchaseChat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'chat_id'     => PurchaseChat::factory(),
            'reviewer_id' => User::factory(),
            'reviewed_id' => User::factory(),
            'rating'      => $this->faker->numberBetween(1, 5),
        ];
    }
}