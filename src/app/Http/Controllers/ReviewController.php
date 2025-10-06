<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewNotificationMail;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'chat_id'     => 'required|exists:purchase_chats,id',
            'reviewed_id' => 'required|exists:users,id',
            'rating'      => 'required|integer|min:1|max:5',
        ]);

        $data['reviewer_id'] = auth()->id();

        $review = Review::create($data);

        $reviewedUser = User::find($data['reviewed_id']);
        Mail::to($reviewedUser->email)->send(new ReviewNotificationMail($review));

        return redirect()
            ->route('items.index')
            ->with('success', '評価を送信しました');
    }
}