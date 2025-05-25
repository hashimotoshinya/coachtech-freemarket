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

        // おすすめタブ（全商品）
        $items = Item::when($keyword, function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%");
        })
        ->with('itemImages')
        ->latest()
        ->get();

        // マイリストタブ（ログインユーザーのお気に入り）
        $myItems = auth()->check()
            ? auth()->user()->favoriteItems()
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%");
                })
                ->with('itemImages')
                ->latest()
                ->get()
            : collect();

        return view('items.index', compact('items', 'myItems', 'keyword'));
    }

    public function show($id)
    {
        $item = Item::with(['comments.user', 'categories'])->findOrFail($id);
        return view('items.show', compact('item'));
    }
}
