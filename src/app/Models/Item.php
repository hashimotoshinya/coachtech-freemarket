<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'brand',
        'description',
        'condition',
        'price',
        'status',
        'user_id',
    ];

    // 商品画像とのリレーション（例: 1商品に複数画像）
    public function itemImages()
    {
        return $this->hasMany(ItemImage::class);
    }

    // 出品者とのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 購入履歴（1商品1購入）
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    // 商品が売り切れか判定
    public function isSoldOut()
    {
        return $this->status === 'sold';
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
