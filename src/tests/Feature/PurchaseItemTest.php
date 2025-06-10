<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use App\Services\StripeService;

class PurchaseItemTest extends TestCase
{
    use RefreshDatabase;

    protected $stripeServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // StripeService のモックを事前バインド
        $this->stripeServiceMock = Mockery::mock(StripeService::class);
        $this->app->instance(StripeService::class, $this->stripeServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_is_redirected_to_stripe_checkout_when_clicking_purchase_button()
    {
        $user = User::factory()->hasProfile()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $this->stripeServiceMock
            ->shouldReceive('createCheckoutSession')
            ->once()
            ->with(Mockery::type(Item::class))
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/test-session',
            ]);

        $response = $this->actingAs($user)->post(route('purchase.complete', $item->id), [
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test-session');
    }

    public function test_user_can_purchase_with_konbini_payment()
    {
        $user = User::factory()->hasProfile()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $fakePaymentIntent = (object)[
            'id' => 'pi_test_123456',
            'amount' => $item->price,
        ];

        $this->stripeServiceMock
            ->shouldReceive('createKonbiniPaymentIntent')
            ->once()
            ->with(Mockery::type(Item::class), Mockery::type(User::class))
            ->andReturn($fakePaymentIntent);

        $response = $this->actingAs($user)->post(route('purchase.complete', $item->id), [
            'payment_method' => 'convenience',
        ]);

        $response->assertViewIs('purchase.konbini');
        $response->assertViewHas('paymentIntent', $fakePaymentIntent);
        $response->assertViewHas('item', $item);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }

    public function test_purchased_item_is_marked_as_sold_on_item_list()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'sold']);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertSee('Sold');
        $response->assertSee($item->title);
    }

    public function test_purchased_item_is_shown_in_profile_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('mypage.index'));

        $response->assertSee($item->title);
    }
}