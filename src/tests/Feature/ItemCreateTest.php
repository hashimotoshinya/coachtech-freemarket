<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_creation_saves_all_required_fields_with_category_pivot()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => '家電']);

        $response = $this->actingAs($user)->post(route('sell.store'), [
            'title' => 'テスト商品',
            'categories' => [$category->id],
            'condition' => '良好', // バリデーションのin:に含まれる値に
            'description' => 'テスト商品の説明です。',
            'price' => 12345,
            'image' => UploadedFile::fake()->image('test.jpg'), // 画像を追加
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'title' => 'テスト商品',
            'condition' => '良好',
            'description' => 'テスト商品の説明です。',
            'price' => 12345,
        ]);

        $itemId = \App\Models\Item::where('title', 'テスト商品')->first()->id;
        $this->assertDatabaseHas('category_item', [
            'category_id' => $category->id,
            'item_id' => $itemId,
        ]);
    }
}