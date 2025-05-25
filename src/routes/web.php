<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;

// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// マイリスト
Route::get('/mylist', [ItemController::class, 'mylist'])->middleware('auth')->name('items.mylist');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');
Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

// 商品購入
Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

// 購入時の住所変更
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

// 商品出品
Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

// 会員登録
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// ログイン／ログアウト
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// マイページ
Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');

// プロフィール編集
Route::get('/mypage/profile', [MypageController::class, 'edit'])->name('mypage.profile.edit');
Route::post('/mypage/profile', [MypageController::class, 'update'])->name('mypage.profile.update');

// お気に入り追加
Route::post('/favorites/{item}', [FavoriteController::class, 'store'])->name('favorites.store')->middleware('auth');

// お気に入り解除
Route::delete('/favorites/{item}', [FavoriteController::class, 'destroy'])->name('favorites.destroy')->middleware('auth');

