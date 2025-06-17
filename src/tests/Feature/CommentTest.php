<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $item->id), [
            'content' => 'これはテストコメントです。',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);
    }

    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item->id), [
            'content' => 'ゲストコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'content' => 'ゲストコメント',
        ]);
    }

    public function test_comment_validation_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('items.show', $item->id))
            ->post(route('comments.store', $item->id), [
                'content' => '',
            ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_validation_max_length()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->from(route('items.show', $item->id))
            ->post(route('comments.store', $item->id), [
                'content' => $longComment,
            ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 0);
    }
}