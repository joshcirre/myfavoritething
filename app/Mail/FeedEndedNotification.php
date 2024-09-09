<?php

namespace App\Mail;

use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedEndedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $feed;

    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    public function build()
    {
        return $this->subject("It's Time to Vote on Your Favorite Thing - ".$this->feed->title)
            ->view('emails.feed-ended');
    }
}
