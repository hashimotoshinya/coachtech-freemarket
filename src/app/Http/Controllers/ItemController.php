<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        // おすすめタブ（全商品）からログインユーザーの出品を除外
        $items = Item::when($keyword, function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%");
            })
            ->when(auth()->check(), function ($query) {
                $query->where('user_id', '!=', auth()->id()); // ログインユーザーの出品を除外
            })
            ->with('itemImages')
            ->latest()
            ->get();

        // マイリストタブ（ログインユーザーのお気に入り）
        $myItems = auth()->check()
            ? auth()->user()->favorites()
                ->where('items.user_id', '!=', auth()->id())
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('items.title', 'like', "%{$keyword}%");
                })
                ->with('itemImages')
                ->latest()
                ->get()
            : collect();

        return view('items.index', compact('items', 'myItems', 'keyword'));
    }
    public function show($id)
    {
        $item = Item::with(['comments.user', 'favoredByUsers','categories'])->findOrFail($id);

        // ログインユーザーに関連するお気に入りを eager load
        $user = auth()->user();
        if ($user) {
            $user->load('favorites');
        }

        return view('items.show', compact('item', 'user'));
    }

    public function purchase($id)
    {
        $item = Item::findOrFail($id);

        // プロフィール情報の取得（ユーザーがログインしている前提）
        $user = auth()->user();
        $profile = $user->profile; // リレーションなら null の可能性あり

        return view('items.purchase', compact('item', 'profile'));
    }
}
