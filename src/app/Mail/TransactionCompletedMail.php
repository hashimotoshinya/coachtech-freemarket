<?php

namespace App\Mail;

use App\Models\PurchaseChat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chat;

    public function __construct(PurchaseChat $chat)
    {
        $this->chat = $chat;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->markdown('emails.transaction_completed');
    }
}