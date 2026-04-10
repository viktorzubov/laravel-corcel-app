<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use Corcel\Model\Comment;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, string $slug): RedirectResponse
    {
        $post = Post::type('post')
            ->published()
            ->slug($slug)
            ->firstOrFail();

        abort_unless($post->comment_status === 'open', 403);

        $validated = $request->validated();

        $comment = new Comment;
        $comment->comment_post_ID = $post->ID;
        $comment->comment_author = $validated['author'];
        $comment->comment_author_email = $validated['email'];
        $comment->comment_author_url = '';
        $comment->comment_author_IP = $request->ip();
        $comment->comment_date = now()->format('Y-m-d H:i:s');
        $comment->comment_date_gmt = now()->utc()->format('Y-m-d H:i:s');
        $comment->comment_content = $validated['content'];
        $comment->comment_karma = 0;
        $comment->comment_approved = '1';
        $comment->comment_agent = $request->userAgent() ?? '';
        $comment->comment_type = 'comment';
        $comment->comment_parent = 0;
        $comment->user_id = 0;
        $comment->save();

        return redirect(route('posts.show', $slug).'#comments')
            ->with('comment_success', 'Your comment has been posted.');
    }
}
