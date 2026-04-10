<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Post;

class AuthorController extends Controller
{
    public function show(string $username)
    {
        $author = Author::byNicename($username)->firstOrFail();

        $posts = Post::type('post')
            ->published()
            ->with(['thumbnail', 'taxonomies.term'])
            ->forAuthor($author)
            ->latest('post_date')
            ->paginate(9);

        return view('authors.show', compact('author', 'posts'));
    }
}
