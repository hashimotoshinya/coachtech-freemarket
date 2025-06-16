<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all(); // カテゴリ一覧を取得
        return view('sells.create', compact('categories'));
    }

    public function store(SellRequest $request)
    {
        // 画像アップロード処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('item_images', 'public'); // storage/app/public/images に保存
        }

        // Itemモデルにデータ保存
        $item = Item::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'brand' => $request->input('brand'),
            'description' => $request->input('description'),
            'condition' => $request->input('condition'),
            'price' => $request->input('price'),
            'status' => 'available',
        ]);

        // カテゴリの中間テーブルを保存（多対多）
        $item->categories()->sync($request->input('categories'));

        // ItemImageテーブルに画像パスを保存
        if ($imagePath) {
            $item->itemImages()->create([
                'image_path' => $imagePath, // ←ここを image_path に統一
            ]);
        }

        return redirect()->route('mypage.index')->with('success', '商品を出品しました。');
    }
}