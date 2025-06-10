<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class); // テスト用にCSRF無効化
    }

    /** @test メールアドレスが未入力の場合、バリデーションメッセージが表示される */
    public function email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test パスワードが未入力の場合、バリデーションメッセージが表示される */
    public function password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test 入力情報が間違っている場合、エラーメッセージが表示される */
    public function incorrect_credentials_show_error()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /** @test 正しい情報でログインできる */
    public function valid_credentials_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'valid@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile'); // 成功後の遷移先に合わせて修正
        $this->assertAuthenticatedAs($user);
    }
}