<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        $items = [
            // CO01〜05 → user1
            [
                'user_email' => 'seller1@example.com',
                'title' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'Armani+Mens+Clock.jpg',
                'condition' => '良好',
            ],
            [
                'user_email' => 'seller1@example.com',
                'title' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_email' => 'seller1@example.com',
                'title' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_email' => 'seller1@example.com',
                'title' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_email' => 'seller1@example.com',
                'title' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'Living+Room+Laptop.jpg',
                'condition' => '良好',
            ],

            // CO06〜10 → user2
            [
                'user_email' => 'seller2@example.com',
                'title' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_email' => 'seller2@example.com',
                'title' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_email' => 'seller2@example.com',
                'title' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_email' => 'seller2@example.com',
                'title' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'user_email' => 'seller2@example.com',
                'title' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'makeup_set.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($items as $itemData) {
            $user = User::where('email', $itemData['user_email'])->first();

            $item = Item::create([
                'user_id' => $user->id,
                'title' => $itemData['title'],
                'price' => $itemData['price'],
                'description' => $itemData['description'],
                'condition' => $itemData['condition'],
                'status' => 'available',
            ]);

            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => 'images/' . $itemData['image'],
            ]);

            $item->categories()->attach(
                $categories->random()->id
            );
        }
    }
}