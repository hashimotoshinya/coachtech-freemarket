<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// マイリスト
Route::get('/mylist', [ItemController::class, 'mylist'])->middleware('auth')->name('items.mylist');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');
Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

// 商品購入
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [ItemController::class, 'purchase'])->name('items.purchase');
});
Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

// 購入時の住所変更
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
Route::get('/purchase/{item}', [PurchaseController::class, 'showPurchaseForm'])->name('items.purchase');

// 購入確定処理
Route::post('/purchase/{item_id}', [PurchaseController::class, 'complete'])->name('purchase.complete');
Route::post('/purchase/complete/{item_id}', [PurchaseController::class, 'complete'])->name('purchase.complete');
Route::get('/purchase/stripe/success/{item_id}', [PurchaseController::class, 'stripeSuccess'])->name('purchase.stripe.success');

// 商品出品
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
});
// 会員登録
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});

// マイページ
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/edit', [MypageController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/update', [MypageController::class, 'update'])->name('mypage.update');
});

// プロフィール編集
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'store'])->name('profile.store');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});
// コメント投稿
Route::post('/comments/{item_id}', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

// お気に入り追加 お気に入り解除
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/favorite', [App\Http\Controllers\FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/items/{item}/favorite', [App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorite.destroy');
});
// メール認証
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // ビューは後述
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証完了
    return redirect('/mypage/profile'); // 認証後の遷移先
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証リンクを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');