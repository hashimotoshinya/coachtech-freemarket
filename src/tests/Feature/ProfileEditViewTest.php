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
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'image_path' => 'profiles/dummy.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区');
        $response->assertSee('profiles/dummy.jpg');
    }
}