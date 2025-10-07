<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\PurchaseChat;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_trading_items_and_message_count_in_mypage()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);
        $chat = PurchaseChat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);
        ChatMessage::factory()->count(3)->create(['chat_id' => $chat->id, 'user_id' => $seller->id]);

        $this->actingAs($buyer);
        $response = $this->get(route('mypage.index'));

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee('3');
    }

    public function test_user_can_navigate_to_trade_chat_from_mypage()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);
        $chat = PurchaseChat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($buyer);
        $response = $this->get( route('purchase_chats.show', $chat->id));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_user_can_switch_to_another_chat_from_sidebar()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item1 = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);
        $item2 = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);

        $chat1 = PurchaseChat::factory()->create(['item_id' => $item1->id, 'buyer_id' => $buyer->id, 'seller_id' => $seller->id]);
        $chat2 = PurchaseChat::factory()->create(['item_id' => $item2->id, 'buyer_id' => $buyer->id, 'seller_id' => $seller->id]);

        $this->actingAs($buyer);

        $response = $this->get(route('purchase_chats.show', $chat1->id));
        $response->assertStatus(200);
        $response->assertSee($item1->name);

        $response = $this->get(route('purchase_chats.show', $chat2->id));
        $response->assertStatus(200);
        $response->assertSee($item2->name);
    }

    public function test_trading_items_are_sorted_by_latest_message()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item1 = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);
        $item2 = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);

        $chat1 = PurchaseChat::factory()->create(['item_id' => $item1->id, 'buyer_id' => $buyer->id, 'seller_id' => $seller->id]);
        $chat2 = PurchaseChat::factory()->create(['item_id' => $item2->id, 'buyer_id' => $buyer->id, 'seller_id' => $seller->id]);

        ChatMessage::factory()->create(['chat_id' => $chat1->id, 'user_id' => $seller->id, 'created_at' => now()->subMinute()]);
        ChatMessage::factory()->create(['chat_id' => $chat2->id, 'user_id' => $seller->id, 'created_at' => now()]);

        $this->actingAs($buyer);
        $response = $this->get(route('mypage.index'));

        $response->assertSeeInOrder([$item2->name, $item1->name]);
    }

    public function test_new_messages_are_shown_with_notification_mark_and_count()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['status' => 'trading', 'user_id' => $seller->id]);
        $chat = PurchaseChat::factory()->create(['item_id' => $item->id, 'buyer_id' => $buyer->id, 'seller_id' => $seller->id]);

        ChatMessage::factory()->count(5)->create(['chat_id' => $chat->id, 'user_id' => $seller->id, 'is_read' => false]);

        $this->actingAs($buyer);
        $response = $this->get(route('mypage.index'));

        $response->assertSee('5');
    }
}