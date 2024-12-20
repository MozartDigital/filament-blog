<?php

use Mozartdigital\FilamentBlog\Exceptions\CannotSendEmail;
use Mozartdigital\FilamentBlog\Listeners\SendBlogPublishedNotification;
use Mozartdigital\FilamentBlog\Mails\BlogPublished;
use Mozartdigital\FilamentBlog\Models\NewsLetter;
use Mozartdigital\FilamentBlog\Models\Post;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->post = Post::factory()->published()->create();
});
it('check event listener is attached to the event', function () {
    // Arrange
    $post = Post::factory()->published()->create();

    // Assert
    Event::fake();
    event(new \Mozartdigital\FilamentBlog\Events\BlogPublished($post));

    Event::assertDispatched(\Mozartdigital\FilamentBlog\Events\BlogPublished::class);

    Event::assertListening(
        \Mozartdigital\FilamentBlog\Events\BlogPublished::class,
        SendBlogPublishedNotification::class
    );

});
it('send new post published email to news letter subscriber', function () {

    //Arrange
    $post = Post::factory()->published()->create();
    NewsLetter::factory()->count(3)->create();
    $subscribers = NewsLetter::all();

    Mail::fake();

    //Assert
    foreach ($subscribers as $subscriber) {
        Mail::send(new BlogPublished($post, $subscriber->email));
        Mail::assertSent(BlogPublished::class);

    }
});

it('includes post details on email template', function () {

    // Arrange
    $post = Post::factory()->published()->create();
    $subscriber = NewsLetter::factory()->create();
    $mail = new BlogPublished($post, $subscriber->email);

    //  Assert
    $mail->assertSeeInHtml('Thank you for subscribing to our blog updates!');
    $mail->assertSeeInHtml($post->title);
    $mail->assertSeeInHtml($post->featurePhoto);
    $mail->assertSeeInHtml('Read More');
    $mail->assertSeeInHtml(route('filamentblog.post.show', $post->slug));

});
it('throws exception if post is not published', function () {
    // Arrange
    $post = Post::factory()->create();
    $subscriber = NewsLetter::factory()->create();
    $mail = new BlogPublished($post, $subscriber->email);

    // Assert
    expect(fn () => $mail->envelope())->toThrow(CannotSendEmail::class);
});
