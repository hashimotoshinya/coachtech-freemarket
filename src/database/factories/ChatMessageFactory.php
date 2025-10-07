<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\PurchaseChat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    public function definition()
    {
        return [
            'chat_id' => PurchaseChat::factory(),
            'user_id' => User::factory(),
            'body' => $this->faker->sentence(),
            'image_path' => null,
            'is_read' => false,
        ];
    }
}