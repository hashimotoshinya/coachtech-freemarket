<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PurchaseChat;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurchaseChatMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_post_message_to_purchase_chat()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $chat = PurchaseChat::factory()->create(['buyer_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('purchase_chats.messages.store', $chat->id), [
            'body' => 'テストメッセージ',
            'image' => UploadedFile::fake()->image('test.png'),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'body' => 'テストメッセージ',
        ]);

        $message = ChatMessage::first();
        Storage::disk('public')->assertExists($message->image_path);
    }

    public function test_body_is_required_and_must_be_less_than_or_equal_to_400_characters()
    {
        $user = User::factory()->create();
        $chat = PurchaseChat::factory()->create(['buyer_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('purchase_chats.messages.store', $chat->id), [
            'body' => '',
        ]);
        $response->assertSessionHasErrors([
            'body' => '本文を入力してください',
        ]);

        $longBody = str_repeat('あ', 401);
        $response = $this->post(route('purchase_chats.messages.store', $chat->id), [
            'body' => $longBody,
        ]);
        $response->assertSessionHasErrors([
            'body' => '本文は400文字以内で入力してください',
        ]);
    }

    public function test_image_must_be_png_or_jpeg()
    {
        $user = User::factory()->create();
        $chat = PurchaseChat::factory()->create(['buyer_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('purchase_chats.messages.store', $chat->id), [
            'body' => '画像テスト',
            'image' => UploadedFile::fake()->create('invalid.gif', 100),
        ]);

        $response->assertSessionHasErrors([
            'image' => '「.png」または「.jpeg」形式でアップロードしてください',
        ]);
    }

    public function test_body_input_is_retained_after_validation_error()
    {
        $user = User::factory()->create();
        $chat = PurchaseChat::factory()->create(['buyer_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->from(route('purchase_chats.show', $chat->id))
            ->post(route('purchase_chats.messages.store', $chat->id), [
                'body' => '保持される本文',
                'image' => UploadedFile::fake()->create('invalid.gif'),
            ]);

        $response->assertRedirect(route('purchase_chats.show', $chat->id));
        $response->assertSessionHasInput('body', '保持される本文');
    }
}