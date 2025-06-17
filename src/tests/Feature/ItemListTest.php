<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    public function guest_can_view_items()
    {
        $item = Item::factory()->create(['title' => 'ゲスト用商品']);

        $response = $this->get('/');

        $response->assertStatus(200)
                ->assertSee('ゲスト用商品');
    }

    public function test_own_items_are_not_visible()
    {
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id, 'title' => '自分の商品']);
        $otherItem = Item::factory()->create(['title' => '他人の商品']);

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('他人の商品');
        $response->assertDontSee('自分の商品');
    }

    public function test_sold_items_show_sold_label()
    {
        $item = Item::factory()->create(['status' => 'sold']);
        Purchase::factory()->create(['item_id' => $item->id]);

        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    public function test_item_image_and_name_are_visible()
    {
        $item = Item::factory()->create(['title' => 'テスト商品']);

        $response = $this->get('/');
        $response->assertSee('テスト商品');
        $response->assertSee('storage/');
    }
}