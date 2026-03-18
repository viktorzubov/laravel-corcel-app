<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Corcel\Model\User;

class AuthorController extends Controller
{
    public function show(string $username)
    {
        $author = User::where('user_nicename', $username)->firstOrFail();

        $posts = Post::type('post')
            ->published()
            ->with(['thumbnail', 'taxonomies.term'])
            ->where('post_author', $author->ID)
            ->latest('post_date')
            ->paginate(9);

        return view('authors.show', compact('author', 'posts'));
    }
}
