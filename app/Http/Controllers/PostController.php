<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Corcel\Model\Comment;
use Corcel\Model\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $activeCategory = $request->query('category');

        $categories = Taxonomy::where('taxonomy', 'category')
            ->with('term')
            ->withCount('posts')
            ->groupBy('term_taxonomy_id')
            ->having('posts_count', '>', 0)
            ->get()
            ->map(fn ($t) => (object) [
                'slug' => $t->term->slug,
                'name' => $t->term->name,
                'count' => $t->posts_count,
            ])
            ->sortByDesc('count')
            ->values();

        $query = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'taxonomies.term', 'meta'])
            ->latest('post_date');

        if ($activeCategory) {
            $query->taxonomy('category', $activeCategory);
        }

        $featured = null;
        if (! $activeCategory && ! $request->query('page')) {
            $featured = $query->first();
            if ($featured) {
                $query->where('ID', '!=', $featured->ID);
            }
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('posts.index', compact('posts', 'featured', 'categories', 'activeCategory'));
    }

    public function show(string $slug)
    {
        $post = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'taxonomies.term'])
            ->slug($slug)
            ->firstOrFail();

        $categorySlugs = array_keys($post->terms['category'] ?? []);

        $related = collect();
        if (! empty($categorySlugs)) {
            $related = Post::type('post')
                ->published()
                ->with(['thumbnail'])
                ->taxonomy('category', $categorySlugs[0])
                ->where('ID', '!=', $post->ID)
                ->latest('post_date')
                ->limit(3)
                ->get();
        }

        $comments = Comment::where('comment_post_ID', $post->ID)
            ->approved()
            ->where('comment_type', 'comment')
            ->where('comment_parent', 0)
            ->with(['replies' => fn ($q) => $q->approved()])
            ->oldest('comment_date')
            ->get();

        $previousPost = Post::type('post')->published()
            ->where('post_date', '<', $post->post_date)
            ->latest('post_date')
            ->first();

        $nextPost = Post::type('post')->published()
            ->where('post_date', '>', $post->post_date)
            ->oldest('post_date')
            ->first();

        $viewCount = $post->viewCount() + 1;
        $post->saveMeta('post_views_count', $viewCount);

        $headings = [];
        $processedContent = preg_replace_callback(
            '/<h2([^>]*)>(.*?)<\/h2>/is',
            function ($m) use (&$headings) {
                $text = strip_tags($m[2]);
                $id = Str::slug($text);
                $headings[] = ['id' => $id, 'text' => $text];

                return '<h2'.$m[1].' id="'.$id.'">'.$m[2].'</h2>';
            },
            $post->content
        );

        $commentCount = $comments->count() + $comments->sum(fn ($c) => $c->replies->count());
        $shareUrl = urlencode(route('posts.show', $post->slug));
        $shareTitle = urlencode($post->title);

        return view('posts.show', compact(
            'post', 'related', 'comments', 'previousPost', 'nextPost',
            'viewCount', 'headings', 'processedContent', 'commentCount', 'shareUrl', 'shareTitle'
        ));
    }
}
