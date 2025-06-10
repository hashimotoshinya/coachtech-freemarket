<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PurchaseAddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_address_and_it_reflects_on_purchase_screen_and_is_saved()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        // ユーザーとしてログイン
        $this->actingAs($user);

        // ① 住所変更画面から POST（セッションに保存される）
        $response = $this->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '123-4567',
            'address' => '東京都港区テスト町',
            'building' => 'テストビル101',
        ]);

        $response->assertRedirect(route('items.purchase', ['item' => $item->id]))
                ->assertSessionHas('purchase_address');

        // ② 購入画面に遷移し、住所が反映されているか確認
        $response = $this->get(route('items.purchase', ['item' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('〒123-4567');
        $response->assertSee('東京都港区テスト町');
        $response->assertSee('テストビル101');

        // ③ Stripe のモック
        $stripeMock = Mockery::mock(\App\Services\StripeService::class);
        $stripeMock->shouldReceive('createKonbiniPaymentIntent')->andReturn('dummy-intent');
        $this->app->instance(\App\Services\StripeService::class, $stripeMock);

        // ④ 購入処理 POST（住所と支払い方法が保存される）
        $purchaseResponse = $this->post(route('purchase.complete', ['item_id' => $item->id]), [
            'payment_method' => 'convenience',
        ]);

        // 成功時には konbini 支払い画面が返る
        $purchaseResponse->assertStatus(200)
                        ->assertViewIs('purchase.konbini');

        // ⑤ DBに保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都港区テスト町',
            'building' => 'テストビル101',
            'payment_method' => 'convenience',
        ]);

        // ⑥ 商品が sold になっていることも確認
        $this->assertEquals('sold', $item->fresh()->status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}