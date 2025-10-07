<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Review;
use App\Models\PurchaseChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewNotificationMail;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_a_review_and_it_is_saved_and_mail_sent()
    {
        Mail::fake();

        $reviewer = User::factory()->create();
        $reviewed = User::factory()->create();
        $chat = PurchaseChat::factory()->create([
            'buyer_id' => $reviewer->id,
            'seller_id' => $reviewed->id,
        ]);

        $this->actingAs($reviewer)
            ->post(route('reviews.store'), [
                'chat_id' => $chat->id,
                'reviewed_id' => $reviewed->id,
                'rating' => 5,
            ])
            ->assertRedirect(route('items.index'))
            ->assertSessionHas('success', '評価を送信しました');

        $this->assertDatabaseHas('reviews', [
            'chat_id' => $chat->id,
            'reviewer_id' => $reviewer->id,
            'reviewed_id' => $reviewed->id,
            'rating' => 5,
        ]);

        Mail::assertSent(ReviewNotificationMail::class, function ($mail) use ($reviewed) {
            return $mail->hasTo($reviewed->email);
        });
    }

    public function test_user_can_see_their_average_rating_in_profile()
    {
        $user = User::factory()->create();
        Review::factory()->count(2)->create([
            'reviewed_id' => $user->id,
            'rating' => 5,
        ]);
        Review::factory()->create([
            'reviewed_id' => $user->id,
            'rating' => 4,
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit', $user->id));

        $response->assertSee('<span class="star full">★</span>', false);
        $response->assertSee('<span class="star half">★</span>', false);
    }

    public function test_no_reviews_means_no_average_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit', $user->id));
        $response->assertDontSee('平均評価');
    }
}