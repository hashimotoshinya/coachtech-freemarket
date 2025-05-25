<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemImage;

class ItemImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imagePaths = [
            'Armani+Mens+Clock.jpg',
            'HDD+Hard+Disk.jpg',
            'iLoveIMG+d.jpg',
            'Leather+Shoes+Product+Photo.jpg',
            'Living+Room+Laptop.jpg',
            'Music+Mic+4632231.jpg',
            'Purse+fashion+pocket.jpg',
            'Tumbler+souvenir.jpg',
            'Waitress+with+Coffee+Grinder.jpg',
            'makeup_set.jpg',
        ];

        $items = Item::all();

        foreach ($items as $index => $item) {
            // エラー防止：画像パスが足りない場合スキップ
            if (!isset($imagePaths[$index])) {
                continue;
            }

            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => 'images/' . $imagePaths[$index],
            ]);
        }
    }
}
