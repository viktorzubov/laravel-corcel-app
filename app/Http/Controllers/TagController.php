<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;

class TagController extends Controller
{
    public function show(string $slug)
    {
        $tag = Tag::slug($slug)->firstOrFail();

        $posts = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'viewCountMeta'])
            ->taxonomy('post_tag', $slug)
            ->latest('post_date')
            ->paginate(9);

        return view('tags.show', compact('tag', 'posts'));
    }
}
