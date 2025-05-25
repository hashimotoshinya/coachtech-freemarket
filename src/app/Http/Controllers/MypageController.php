<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function edit()
    {
        return view('mypage.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました');
    }
}
