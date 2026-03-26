<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\SearchHighlighter;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->string('q')->trim()->value();

        $posts = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'taxonomies.term'])
            ->where(function ($q) use ($query) {
                $q->where('post_title', 'like', "%{$query}%")
                    ->orWhere('post_content', 'like', "%{$query}%")
                    ->orWhere('post_excerpt', 'like', "%{$query}%");
            })
            ->latest('post_date')
            ->paginate(9)
            ->withQueryString();

        $highlighter = new SearchHighlighter($query);

        return view('search', compact('posts', 'query', 'highlighter'));
    }
}
