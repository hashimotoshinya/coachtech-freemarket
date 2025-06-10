<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $profileExists = $profile ? true : false;

        return view('mypage.profile', compact('user', 'profile', 'profileExists'));
    }

    public function store(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // 保存先を profile_images に変更
            $data['image_path'] = $request->file('image')->store('profile_images', 'public');
        }

        $profile = new Profile($data);
        $user->profile()->save($profile);

        // ユーザー名も更新
        $user->update(['name' => $data['name']]);

        return redirect()->route('items.index')->with('success', 'プロフィールを登録しました');
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // 旧画像の削除（存在する場合）
            if ($user->profile && $user->profile->image_path) {
                Storage::disk('public')->delete($user->profile->image_path);
            }
            // 新しい画像を保存（profile_images ディレクトリ）
            $data['image_path'] = $request->file('image')->store('profile_images', 'public');
        }

        // プロフィール情報を更新または新規作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        // ユーザー名の更新
        $user->update(['name' => $data['name']]);

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました');
    }
}