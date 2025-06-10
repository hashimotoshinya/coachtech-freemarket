<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Item;

class MypageController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('profile');
        $items = $user->items()->with('itemImages')->get();
        // 購入した商品
        $boughtItems = Purchase::with('item.itemImages')
            ->where('user_id', $user->id)
            ->get()
            ->pluck('item'); // itemだけ取り出す
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
