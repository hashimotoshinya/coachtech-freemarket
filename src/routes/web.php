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

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/purchase/{item}', [PurchaseController::class, 'showPurchaseForm'])->name('items.purchase');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'complete'])->name('purchase.complete');

    Route::get('/purchase/stripe/success/{item_id}', [PurchaseController::class, 'stripeSuccess'])->name('purchase.stripe.success');

    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    Route::prefix('mypage')->group(function () {
        Route::get('/', [MypageController::class, 'index'])->name('mypage.index');
        Route::get('/edit', [MypageController::class, 'edit'])->name('mypage.edit');
        Route::post('/update', [MypageController::class, 'update'])->name('mypage.update');

        Route::middleware('verified')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        });
    });

    Route::get('/mylist', [ItemController::class, 'mylist'])->name('items.mylist');

    Route::post('/items/{item}/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');

    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

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