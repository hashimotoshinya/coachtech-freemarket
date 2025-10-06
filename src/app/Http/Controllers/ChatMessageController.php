<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseChat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatMessageRequest;

class ChatMessageController extends Controller
{
    public function storeMessage(StoreChatMessageRequest $request, PurchaseChat $chat)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        ChatMessage::create([
            'chat_id'    => $chat->id,
            'user_id'    => Auth::id(),
            'body'       => $request->body,
            'image_path' => $imagePath,
            'is_read'    => false,
        ]);

        return redirect()->route('purchase_chats.show', $chat->id)
            ->with('success', 'メッセージを送信しました。');
    }

    public function edit(ChatMessage $message)
    {
        return view('purchase_chats.edit', compact('message'));
    }

    public function update(StoreChatMessageRequest $request, ChatMessage $message)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($message->image_path) {
                Storage::disk('public')->delete($message->image_path);
            }
            $data['image_path'] = $request->file('image')->store('chat_images', 'public');
        }

        $message->update([
            'body' => $data['body'],
            'image_path' => $data['image_path'] ?? $message->image_path,
        ]);

        return redirect()->route('purchase_chats.show', $message->chat_id)
            ->with('success', 'メッセージを更新しました。');
    }

    public function destroy(ChatMessage $message)
    {
        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }
}