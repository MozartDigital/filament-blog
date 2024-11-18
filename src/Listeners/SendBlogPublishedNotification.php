<?php

namespace Mozartdigital\FilamentBlog\Listeners;

use Mozartdigital\FilamentBlog\Mails\BlogPublished;
use Mozartdigital\FilamentBlog\Models\NewsLetter;
use Illuminate\Support\Facades\Mail;

class SendBlogPublishedNotification
{
    public function handle($event)
    {
        $subscribers = NewsLetter::subscribed()->get();

        foreach ($subscribers as $subscriber) {
            Mail::queue(new BlogPublished($event->post, $subscriber->email));
        }
    }
}
