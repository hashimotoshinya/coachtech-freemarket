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
        $user = User::factory()->create();

        // 3カテゴリ作成
        $categories = Category::factory()->count(3)->create();

        // 商品一覧
        $items = [
            [
                'title' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'Armani+Mens+Clock.jpg',
                'condition' => '良好',
            ],
            [
                'title' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'title' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'title' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'title' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'Living+Room+Laptop.jpg',
                'condition' => '良好',
            ],
            [
                'title' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'title' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'title' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'title' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'title' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'makeup_set.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($items as $index => $itemData) {
            // 商品登録
            $item = Item::create([
                'user_id' => $user->id,
                'title' => $itemData['title'],
                'price' => $itemData['price'],
                'description' => $itemData['description'],
                'condition' => $itemData['condition'],
                'status' => 'available',
            ]);

            // 商品画像登録
            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => 'images/' . $itemData['image'],
            ]);

            // カテゴリを1つ紐付け（ランダムでもOK）
            $item->categories()->attach(
                $categories[$index % $categories->count()]->id
            );
        }
    }
}