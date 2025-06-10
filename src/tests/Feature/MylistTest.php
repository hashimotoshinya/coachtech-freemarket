<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test マイリストにいいね商品だけが表示される */
    public function test_only_favorited_items_are_visible()
    {
        $user = User::factory()->create();

        $likedItem = Item::factory()->create(['title' => 'いいね商品']);
        $otherItem = Item::factory()->create(['title' => '未いいね商品']);

        $user->favorites()->attach($likedItem->id);

        $response = $this->actingAs($user)->get('/');

        $html = $response->getContent();

        // mylistエリアだけを抽出
        $start = strpos($html, '<div id="mylist"');
        $end = strpos($html, '</div>', $start);
        $mylistHtml = substr($html, $start, $end - $start);

        $this->assertStringContainsString('いいね商品', $mylistHtml);
        $this->assertStringNotContainsString('未いいね商品', $mylistHtml);
    }

    /** @test 売り切れ商品にはSOLD表示される */
    public function test_sold_items_show_sold_label_in_mylist()
    {
        $user = User::factory()->create();

        $soldItem = Item::factory()->create(['title' => '売り切れ商品', 'status' => 'sold']);
        $user->favorites()->attach($soldItem->id);

        // 売り切れ商品に対応する購入レコードを作成
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('売り切れ商品', false);
        $response->assertSee('Sold', false);
    }

    /** @test マイリストに自分の商品が表示されない */
    public function test_own_items_are_not_shown_in_mylist()
    {
        $user = User::factory()->create();

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'title' => '自分の商品'
        ]);

        $user->favorites()->attach($ownItem->id);

        $response = $this->actingAs($user)->get('/');

        $response->assertDontSee('自分の商品', false);
    }

    /** @test 未認証ユーザーはマイリストを見られない */
    public function test_guest_cannot_see_mylist_items()
    {
        $response = $this->get('/');

        $response->assertSee('ログイン', false);
    }
}