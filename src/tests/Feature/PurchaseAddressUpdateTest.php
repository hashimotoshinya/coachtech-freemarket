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
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '123-4567',
            'address' => '東京都港区テスト町',
            'building' => 'テストビル101',
        ]);

        $response->assertRedirect(route('items.purchase', ['item' => $item->id]))
                ->assertSessionHas('purchase_address');

        $response = $this->get(route('items.purchase', ['item' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('〒123-4567');
        $response->assertSee('東京都港区テスト町');
        $response->assertSee('テストビル101');

        $stripeMock = Mockery::mock(\App\Services\StripeService::class);
        $stripeMock->shouldReceive('createKonbiniPaymentIntent')->andReturn('dummy-intent');
        $this->app->instance(\App\Services\StripeService::class, $stripeMock);

        $purchaseResponse = $this->post(route('purchase.complete', ['item_id' => $item->id]), [
            'payment_method' => 'convenience',
        ]);

        $purchaseResponse->assertStatus(200)
                        ->assertViewIs('purchase.konbini');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都港区テスト町',
            'building' => 'テストビル101',
            'payment_method' => 'convenience',
        ]);

        $this->assertEquals('sold', $item->fresh()->status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}