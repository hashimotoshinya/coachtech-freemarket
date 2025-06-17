<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_search_keyword_is_preserved_in_mylist_tab()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['title' => 'PlayStation']);
        $user->favorites()->attach($item->id);

        $response = $this->actingAs($user)->get('/?keyword=Play');

        $html = $response->getContent();

        $start = strpos($html, '<div id="mylist"');
        $end = strpos($html, '</div>', $start);
        $mylistHtml = substr($html, $start, $end - $start);

        $this->assertStringContainsString('PlayStation', $mylistHtml);

        $response->assertSee('value="Play"', false);
    }
}