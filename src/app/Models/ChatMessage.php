<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_id',
        'body',
        'image_path',
        'is_read',
    ];

    public function chat()
    {
        return $this->belongsTo(PurchaseChat::class, 'chat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}