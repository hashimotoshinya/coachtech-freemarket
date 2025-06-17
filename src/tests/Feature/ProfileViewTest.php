<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_profile_information_is_displayed_correctly()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'image_path' => 'profiles/dummy.jpg',
        ]);

        $items = Item::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'available',
        ]);

        $seller = User::factory()->create();
        $boughtItems = Item::factory()->count(2)->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);

        foreach ($boughtItems as $item) {
            Purchase::factory()->create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('mypage.index'));

        $response->assertStatus(200);

        $response->assertSee('テスト太郎');
        $response->assertSee('profiles/');

        foreach ($items as $item) {
            $response->assertSee($item->title);
        }

        foreach ($boughtItems as $item) {
            $response->assertSee($item->title);
        }
    }
}