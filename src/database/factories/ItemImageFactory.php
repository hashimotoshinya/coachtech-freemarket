<?php

namespace Database\Factories;

use App\Models\ItemImage;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemImageFactory extends Factory
{
    protected $model = ItemImage::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'image_path' => 'item_images/' . $this->faker->uuid . '.jpg', // ダミー画像パス
        ];
    }
}