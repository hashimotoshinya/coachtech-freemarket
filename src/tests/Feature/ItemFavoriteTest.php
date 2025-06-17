<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemFavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_favorite_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->get(route('items.show', $item->id));
        $response->assertStatus(200);
        $response->assertSee('⭐︎');
        $response->assertDontSee('liked');

        $response = $this->actingAs($user)->post(route('favorite.store', $item));
        $response->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.show', $item->id));
        $response->assertSee('⭐️');
        $response->assertSee('liked');
        $response->assertSee('1');
    }

    public function user_can_unfavorite_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $user->favorites()->attach($item->id);
        $this->assertEquals(1, $item->favorites()->count());

        $response = $this->actingAs($user)->delete(route('favorite.destroy', $item));
        $response->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.show', $item->id));
        $response->assertSee('⭐︎');
        $response->assertDontSee('liked');
        $response->assertSee('0');
    }
}