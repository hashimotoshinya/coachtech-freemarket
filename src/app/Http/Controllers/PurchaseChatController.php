<?php

namespace App\Http\Controllers;

use App\Models\PurchaseChat;
use App\Models\ChatMessage;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class PurchaseChatController extends Controller
{
    public function show($id)
    {
        if (request()->route()->getName() === 'purchase_chats.show_by_item') {
            $chat = PurchaseChat::where('item_id', $id)
                ->where(function($q) {
                    $q->where('buyer_id', auth()->id())
                        ->orWhere('seller_id', auth()->id());
                })
                ->with(['item', 'messages.user'])
                ->firstOrFail();
        } else {
            $chat = PurchaseChat::with(['item', 'messages.user'])->findOrFail($id);
        }

        $chat->messages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $userId = auth()->id();

        $chats = PurchaseChat::with([
                'item',
                'messages' => function ($q) {
                    $q->latest()->take(1);
                }
            ])
            ->withCount([
                'messages as unread_count' => function ($q) use ($userId) {
                    $q->where('user_id', '!=', $userId)
                        ->where('is_read', false);
                }
            ])
            ->withMax('messages', 'created_at')
            ->where(function($q) use ($userId) {
                $q->where(function($q2) use ($userId) {
                    $q2->where('buyer_id', $userId)
                        ->where('deleted_by_buyer', false);
                })->orWhere(function($q2) use ($userId) {
                    $q2->where('seller_id', $userId)
                        ->where('deleted_by_seller', false);
                });
            })
            ->orderByDesc('unread_count')
            ->orderByDesc('messages_max_created_at')
            ->get();

        $partner = $chat->buyer_id === $userId
            ? $chat->seller
            : $chat->buyer;

        $hasReviewed = $chat->reviews()
            ->where('reviewer_id', $userId)
            ->exists();

        $showReviewModal = $chat->completed_at !== null && !$hasReviewed;

        return view('purchase_chats.show', [
            'chat'            => $chat,
            'chats'           => $chats,
            'partner'         => $partner,
            'showReviewModal' => $showReviewModal,
        ]);
    }

    public function createChat($itemId)
    {
        $item = Item::findOrFail($itemId);

        if ($item->user_id === auth()->id()) {
            return back()->with('error', '自分の商品にはチャットできません');
        }

        $chat = PurchaseChat::where('item_id', $item->id)
            ->where('buyer_id', auth()->id())
            ->first();

        if (!$chat) {
            $chat = PurchaseChat::create([
                'item_id'   => $item->id,
                'buyer_id'  => auth()->id(),
                'seller_id' => $item->user_id,
            ]);

            $item->update(['status' => 'trading']);
        }

        return redirect()->route('purchase_chats.show', $chat->id)
            ->with('success', 'チャットを開始しました');
    }

    public function complete(PurchaseChat $chat)
    {
        if ($chat->buyer_id !== auth()->id()) {
            abort(403, 'この操作は許可されていません');
        }

        $chat->update([
            'completed_at' => now(),
        ]);

        $seller = User::find($chat->seller_id);
        Mail::to($seller->email)->send(new TransactionCompletedMail($chat));

        return redirect()->route('purchase_chats.show', $chat->id);
    }

    public function destroy(PurchaseChat $chat)
    {
        $userId = auth()->id();

        if ($chat->buyer_id === $userId) {
            $chat->update(['deleted_by_buyer' => true]);
        } elseif ($chat->seller_id === $userId) {
            $chat->update(['deleted_by_seller' => true]);
        } else {
            abort(403, '権限がありません');
        }

        return redirect()->route('mypage.index')->with('success', '取引を非表示にしました');
    }
}