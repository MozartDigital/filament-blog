<?php

use Mozartdigital\FilamentBlog\Models\Post;
use Mozartdigital\FilamentBlog\Models\SeoDetail;

it('belongs to post', function () {
    // Arrange
    $post = Post::factory()->has(SeoDetail::factory())->create();

    // Act & Assert
    expect($post->seoDetail)->toBeInstanceOf(SeoDetail::class);
});
