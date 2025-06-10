<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test 商品名で部分一致検索ができる */
    public function test_items_can_be_searched_by_partial_match()
    {
        Item::factory()->create(['title' => 'Apple Watch']);
        Item::factory()->create(['title' => 'Galaxy Phone']);
        Item::factory()->create(['title' => 'MacBook Pro']);

        $response = $this->get('/?keyword=Mac');

        $response->assertSee('MacBook Pro');
        $response->assertDontSee('Apple Watch');
        $response->assertDontSee('Galaxy Phone');
    }

    /** @test マイリストでも検索キーワードが保持されている */
    public function test_search_keyword_is_preserved_in_mylist_tab()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['title' => 'PlayStation']);
        $user->favorites()->attach($item->id);

        $response = $this->actingAs($user)->get('/?keyword=Play');

        $html = $response->getContent();

        // mylistエリアだけを抽出して検証
        $start = strpos($html, '<div id="mylist"');
        $end = strpos($html, '</div>', $start);
        $mylistHtml = substr($html, $start, $end - $start);

        $this->assertStringContainsString('PlayStation', $mylistHtml);

        // フォーム内に検索キーワードが保持されているか
        $response->assertSee('value="Play"', false);
    }
}