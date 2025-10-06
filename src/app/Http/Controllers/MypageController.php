<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\PurchaseChat;
use App\Models\ChatMessage;

class MypageController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('profile');

        $items = $user->items()->with('itemImages')->get();

        $boughtItems = Purchase::with('item.itemImages')
            ->where('user_id', $user->id)
            ->get()
            ->pluck('item');

        $soldItems = $items->filter(fn($item) => $item->purchased_flg === true);

        $tradingChats = PurchaseChat::where(function($q) use ($user) {
            $q->where(function($q2) use ($user) {
                $q2->where('buyer_id', $user->id)
                    ->where('deleted_by_buyer', false);
            })->orWhere(function($q2) use ($user) {
                $q2->where('seller_id', $user->id)
                    ->where('deleted_by_seller', false);
            });
        })
        ->whereHas('item', fn($q) => $q->where('status', 'trading'))
        ->with(['item.itemImages'])
        ->withCount([
            'messages as unread_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->where('is_read', false);
            }
        ])
        ->get();

        $unreadCount = \App\Models\ChatMessage::whereIn('chat_id', $tradingChats->pluck('id'))
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return view('mypage.index', compact(
            'user',
            'items',
            'boughtItems',
            'soldItems',
            'tradingChats',
            'unreadCount'
        ));
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
