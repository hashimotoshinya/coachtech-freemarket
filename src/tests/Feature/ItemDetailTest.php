<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ItemImage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test 商品詳細ページにすべての情報が表示される */
    public function test_item_detail_page_displays_all_required_information()
    {
        $user = User::factory()->create(['name' => 'コメントユーザー']);

        $item = Item::factory()->create([
            'title' => 'Nintendo Switch',
            'brand' => '任天堂',
            'price' => 29980,
            'description' => '最新モデルです。',
            'condition' => '新品',
        ]);

        // カテゴリ2つ
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id'));

        // 商品画像
        ItemImage::factory()->create([
            'item_id' => $item->id,
            'image_path' => 'item_images/test_image.jpg',
        ]);

        // コメント
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => '興味あります！',
        ]);

        // いいね
        $favUser = User::factory()->create();
        $favUser->favorites()->attach($item->id);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('Nintendo Switch');
        $response->assertSee('任天堂');
        $response->assertSee('29,980');
        $response->assertSee('最新モデルです。');
        $response->assertSee('新品');
        $response->assertSee('興味あります！');
        $response->assertSee('コメントユーザー');
        $response->assertSee('test_image.jpg'); // 画像のパス
        $response->assertSee($categories[0]->name);
        $response->assertSee($categories[1]->name);

        // いいね数・コメント数の表示（数値で判定する場合）
        $response->assertSee('⭐');
        $response->assertSee('1');
        $response->assertSee('💬');
        $response->assertSee('1');
    }

    /** @test 複数カテゴリが商品詳細ページに表示される */
    public function test_multiple_categories_are_displayed_on_item_detail_page()
    {
        $item = Item::factory()->create();
        $categories = Category::factory()->count(3)->create();

        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}