<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Corcel\Model\Comment;
use Corcel\Model\Taxonomy;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    public function index(Request $request): View
    {
        $activeCategory = $request->query('category');

        $categories = collect(Cache::remember('post_categories', 3600, function () {
            return Taxonomy::where('taxonomy', 'category')
                ->with('term')
                ->withCount('posts')
                ->groupBy('term_taxonomy_id')
                ->having('posts_count', '>', 0)
                ->get()
                ->map(fn ($t) => [
                    'slug' => $t->term->slug,
                    'name' => $t->term->name,
                    'count' => $t->posts_count,
                ])
                ->sortByDesc('count')
                ->values()
                ->all();
        }))->map(fn (array $item) => (object) $item);

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

    public function show(string $slug): View
    {
        $post = Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'taxonomies.term'])
            ->slug($slug)
            ->firstOrFail();

        $related = Post::relatedTo($post)->get();

        $comments = Comment::where('comment_post_ID', $post->ID)
            ->approved()
            ->where('comment_type', 'comment')
            ->where('comment_parent', 0)
            ->with(['replies' => fn ($q) => $q->approved()])
            ->oldest('comment_date')
            ->get();

        $previousPost = Post::previousTo($post)->first();
        $nextPost = Post::nextTo($post)->first();

        $viewCount = $this->postService->incrementViewCount($post);
        $headings = $this->postService->extractHeadings($post->content);
        $processedContent = $this->postService->processContent($post->content);
        $commentCount = $comments->count() + $comments->sum(fn ($c) => $c->replies->count());
        $shareUrl = $this->postService->shareUrl($post);
        $shareTitle = $this->postService->shareTitle($post);

        return view('posts.show', compact(
            'post', 'related', 'comments', 'previousPost', 'nextPost',
            'viewCount', 'headings', 'processedContent', 'commentCount', 'shareUrl', 'shareTitle'
        ));
    }
}
