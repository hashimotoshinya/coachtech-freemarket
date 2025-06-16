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
        $categories = Category::all();
        return view('sells.create', compact('categories'));
    }

    public function store(SellRequest $request)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('item_images', 'public');
        }

        $item = Item::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'brand' => $request->input('brand'),
            'description' => $request->input('description'),
            'condition' => $request->input('condition'),
            'price' => $request->input('price'),
            'status' => 'available',
        ]);

        $item->categories()->sync($request->input('categories'));

        if ($imagePath) {
            $item->itemImages()->create([
                'image_path' => $imagePath,
            ]);
        }

        return redirect()->route('mypage.index')->with('success', '商品を出品しました。');
    }
}