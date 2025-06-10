<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemFavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test いいね押下で登録され、いいね数が増加し色が変わる */
    public function user_can_favorite_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ログイン状態で商品詳細ページを表示
        $response = $this->actingAs($user)->get(route('items.show', $item->id));
        $response->assertStatus(200);
        $this->assertEquals(0, $item->favorites()->count());

        // いいね押下
        $response = $this->actingAs($user)->post(route('favorite.store', $item->id));
        $response->assertRedirect(); // リダイレクト確認

        // データベースに登録されているか確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 再度ページ表示して「1件のいいね」になっているか確認
        $response = $this->actingAs($user)->get(route('items.show', $item->id));
        $response->assertSee('⭐');
        $response->assertSee('1');

        // アイコンの色変更が表示されている（例: text-yellow-500）
        $response->assertSee('text-yellow-500');
    }

    /** @test いいね再押下で解除され、いいね数が減少し色が戻る */
    public function user_can_unfavorite_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 先にいいねしておく
        $user->favorites()->attach($item->id);

        $this->assertEquals(1, $item->favorites()->count());

        // いいね解除リクエスト送信
        $response = $this->actingAs($user)->delete(route('favorite.destroy', $item->id));
        $response->assertRedirect();

        // favoritesテーブルから削除されているか
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ページに戻って「0件のいいね」になっているか確認
        $response = $this->actingAs($user)->get(route('items.show', $item->id));
        $response->assertSee('⭐');
        $response->assertSee('0');

        // アイコンの色が非アクティブ（例: text-gray-400）に戻っていること
        $response->assertSee('text-gray-400');
    }
}