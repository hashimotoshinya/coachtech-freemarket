<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 出品者1
        $user1 = User::create([
            'name' => 'Seller One',
            'email' => 'seller1@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // 出品者2
        $user2 = User::create([
            'name' => 'Seller Two',
            'email' => 'seller2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // 出品なしのユーザー
        $user3 = User::create([
            'name' => 'No Items User',
            'email' => 'noitems@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}