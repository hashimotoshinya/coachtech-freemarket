<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    ItemController,
    Auth\RegisteredUserController,
    Auth\AuthenticatedSessionController,
    PurchaseController,
    SellController,
    MypageController,
    ProfileController,
    CommentController,
    FavoriteController
};

/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/

// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

/*
|--------------------------------------------------------------------------
| 認証関連（Fortify）
|--------------------------------------------------------------------------
*/

// 会員登録
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// ログイン・ログアウト・パスワード・2段階認証など → Fortify側で管理（Route::middleware(['auth']) に依存）

/*
|--------------------------------------------------------------------------
| ログイン必須ルート
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ダッシュボード（必要に応じて）
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // 商品購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'showPurchaseForm'])->name('items.purchase');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'complete'])->name('purchase.complete');
    Route::post('/purchase/complete/{item_id}', [PurchaseController::class, 'complete']); // 重複ルート（name なし or 削除してOK）

    // Stripe決済完了
    Route::get('/purchase/stripe/success/{item_id}', [PurchaseController::class, 'stripeSuccess'])->name('purchase.stripe.success');

    // 購入時の住所変更
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 商品出品
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // マイページ
    Route::prefix('mypage')->group(function () {
        Route::get('/', [MypageController::class, 'index'])->name('mypage.index');
        Route::get('/edit', [MypageController::class, 'edit'])->name('mypage.edit');
        Route::post('/update', [MypageController::class, 'update'])->name('mypage.update');

        // プロフィール（メール認証必須）
        Route::middleware('verified')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        });
    });

    // マイリスト（お気に入り一覧）
    Route::get('/mylist', [ItemController::class, 'mylist'])->name('items.mylist');

    // お気に入り登録・解除
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');

    // コメント投稿（ログイン必須）
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    /*
    |--------------------------------------------------------------------------
    | メール認証関連
    |--------------------------------------------------------------------------
    */
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証リンクを再送しました。');
    })->middleware('throttle:6,1')->name('verification.send');
});