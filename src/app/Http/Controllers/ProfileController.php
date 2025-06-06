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
            $data['image_path'] = $request->file('image')->store('profiles', 'public');
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
            if ($user->profile && $user->profile->image_path) {
                Storage::disk('public')->delete($user->profile->image_path);
            }
            $data['image_path'] = $request->file('image')->store('profiles', 'public');
        }

        $user->profile()->updateOrCreate(['user_id' => $user->id], $data);
        $user->update(['name' => $data['name']]);

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました');
    }
}
