<?php

namespace Mozartdigital\FilamentBlog\Http\Controllers;

use Mozartdigital\FilamentBlog\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'comment' => 'required|min:3|max:500',
        ]);

        $post->comments()->create([
            'comment' => $request->comment,
            'user_id' => $request->user()->id,
            'approved' => false,
        ]);

        return redirect()
            ->route('filamentblog.post.show', $post)
            ->with('success', 'Comment submitted for approval.');
    }
}
