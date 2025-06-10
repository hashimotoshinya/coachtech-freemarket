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

        // 1. ユーザー作成
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        // ダミー画像は作成せず、パスのみ保存
        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'image_path' => 'profiles/dummy.jpg',
        ]);

        // 2. 出品商品を3件作成
        $items = Item::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'available',
        ]);

        // 3. 他ユーザー・購入済商品作成
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

        // 4. ログインしてマイページにアクセス
        $response = $this->actingAs($user)->get(route('mypage.index'));

        // 5. 各種表示確認
        $response->assertStatus(200);

        // ユーザー名とプロフィール画像
        $response->assertSee('テスト太郎');
        $response->assertSee('profiles/'); // プロフィール画像の保存先

        // 出品商品が表示されているか
        foreach ($items as $item) {
            $response->assertSee($item->title);
        }

        // 購入商品が表示されているか
        foreach ($boughtItems as $item) {
            $response->assertSee($item->title);
        }
    }
}