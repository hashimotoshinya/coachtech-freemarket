<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $items = $user->items()->with('itemImages')->get();
        $boughtItems = $user->purchases()->with('item.itemImages')->get(); // 購入した商品（中に item を持つ）
        // 売れた商品（例：purchased_flg が true の商品）
        $soldItems = $items->filter(function ($item) {
            return $item->purchased_flg === true;
        });

        return view('mypage.index', compact('user', 'items', 'boughtItems'));

    }

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
