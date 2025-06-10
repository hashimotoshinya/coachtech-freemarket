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

    /** @test ログインユーザーがコメントを投稿できる */
    public function user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $item->id), [
            'content' => 'これはテストコメントです。',
        ]);

        $response->assertRedirect(); // 成功時リダイレクト

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);
    }

    /** @test ゲストはコメントを投稿できない */
    public function guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item->id), [
            'content' => 'ゲストコメント',
        ]);

        $response->assertRedirect(route('login')); // Fortify使用を前提に/loginへ

        $this->assertDatabaseMissing('comments', [
            'content' => 'ゲストコメント',
        ]);
    }

    /** @test コメントが空の場合、バリデーションエラーになる */
    public function comment_validation_required()
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

    /** @test コメントが255文字を超える場合、バリデーションエラーになる */
    public function comment_validation_max_length()
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