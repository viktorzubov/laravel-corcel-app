<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Corcel\Model\Taxonomy;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Taxonomy::category()->slug($slug)->firstOrFail();

        $posts = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'viewCountMeta'])
            ->taxonomy('category', $slug)
            ->latest('post_date')
            ->paginate(9);

        return view('categories.show', compact('category', 'posts'));
    }
}
