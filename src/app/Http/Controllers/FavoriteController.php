<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Item $item)
    {
        auth()->user()->favorites()->attach($item->id);
        return back();
    }

    public function destroy(Item $item)
    {
        auth()->user()->favorites()->detach($item->id);
        return back();
    }
}
