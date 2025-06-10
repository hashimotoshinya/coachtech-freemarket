<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileEditViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_form_shows_initial_values()
    {
        // 1. ユーザー作成
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        // プロフィール作成（ダミー画像パス含む）
        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'image_path' => 'profiles/dummy.jpg',
        ]);

        // 2. ログインしてプロフィール編集ページへアクセス
        $response = $this->actingAs($user)->get(route('profile.edit'));

        // 3. 各項目の初期値が正しく表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('テスト太郎'); // ユーザー名
        $response->assertSee('123-4567');   // 郵便番号
        $response->assertSee('東京都新宿区'); // 住所
        $response->assertSee('profiles/dummy.jpg'); // プロフィール画像パス
    }
}