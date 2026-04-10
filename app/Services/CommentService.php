<?php

namespace App\Services;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use Corcel\Model\Comment;

class CommentService
{
    public function store(Post $post, StoreCommentRequest $request): Comment
    {
        $validated = $request->validated();
        $user = $request->user();

        $comment = new Comment;
        $comment->comment_post_ID = $post->ID;
        $comment->comment_author = $validated['author'] ?? $user?->name ?? '';
        $comment->comment_author_email = $validated['email'] ?? $user?->email ?? '';
        $comment->comment_author_url = '';
        $comment->comment_author_IP = $request->ip();
        $comment->comment_date = now()->format('Y-m-d H:i:s');
        $comment->comment_date_gmt = now()->utc()->format('Y-m-d H:i:s');
        $comment->comment_content = $validated['content'];
        $comment->comment_karma = 0;
        $comment->comment_approved = '1';
        $comment->comment_agent = $request->userAgent() ?? '';
        $comment->comment_type = 'comment';
        $comment->comment_parent = (int) ($validated['parent_id'] ?? 0);
        $comment->user_id = $user?->id ?? 0;
        $comment->save();

        return $comment;
    }
}
