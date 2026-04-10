<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService) {}

    public function store(StoreCommentRequest $request, string $slug): RedirectResponse
    {
        $post = Post::type('post')
            ->published()
            ->slug($slug)
            ->firstOrFail();

        abort_unless($post->comment_status === 'open', 403);

        $this->commentService->store($post, $request);

        return redirect(route('posts.show', $slug).'#comments')
            ->with('comment_success', 'Your comment has been posted.');
    }
}
