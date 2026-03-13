<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Corcel\Model\Taxonomy;

class TagController extends Controller
{
    public function show(string $slug)
    {
        $tag = Taxonomy::where('taxonomy', 'post_tag')->slug($slug)->firstOrFail();

        $posts = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author'])
            ->taxonomy('post_tag', $slug)
            ->latest('post_date')
            ->paginate(9);

        return view('tags.show', compact('tag', 'posts'));
    }
}
