<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test ユーザーは正常にログアウトできる */
    public function user_can_logout_successfully()
    {
        // ユーザーを作成してログイン状態にする
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        // ログアウト後のリダイレクトを確認（Fortifyデフォルトでは `/`）
        $response->assertRedirect('/');

        // 認証されていない状態であることを確認
        $this->assertGuest();
    }
}