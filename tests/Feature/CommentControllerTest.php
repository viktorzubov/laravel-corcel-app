<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\CreatesWordPressData;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{

    public function test_store_creates_approved_comment(): void
    {
        $this->createPost(['post_name' => 'commentable-post', 'comment_status' => 'open']);

        $response = $this->post(route('posts.comments.store', 'commentable-post'), [
            'author'  => 'John Doe',
            'email'   => 'john@example.com',
            'content' => 'Great article, thanks!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'comment_author'       => 'John Doe',
            'comment_author_email' => 'john@example.com',
            'comment_content'      => 'Great article, thanks!',
            'comment_approved'     => '1',
        ], 'wordpress');
    }

    public function test_store_redirects_back_to_post_comments_fragment(): void
    {
        $this->createPost(['post_name' => 'redirect-post', 'comment_status' => 'open']);

        $response = $this->post(route('posts.comments.store', 'redirect-post'), [
            'author'  => 'Jane',
            'email'   => 'jane@example.com',
            'content' => 'Interesting!',
        ]);

        $response->assertRedirectContains(route('posts.show', 'redirect-post'));
    }

    public function test_store_returns_validation_error_when_author_missing(): void
    {
        $this->createPost(['post_name' => 'val-post', 'comment_status' => 'open']);

        $response = $this->post(route('posts.comments.store', 'val-post'), [
            'email'   => 'x@example.com',
            'content' => 'Missing author',
        ]);

        $response->assertSessionHasErrors('author');
    }

    public function test_store_returns_validation_error_for_invalid_email(): void
    {
        $this->createPost(['post_name' => 'email-post', 'comment_status' => 'open']);

        $response = $this->post(route('posts.comments.store', 'email-post'), [
            'author'  => 'Bob',
            'email'   => 'not-an-email',
            'content' => 'Hello',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_returns_validation_error_when_content_missing(): void
    {
        $this->createPost(['post_name' => 'content-post', 'comment_status' => 'open']);

        $response = $this->post(route('posts.comments.store', 'content-post'), [
            'author' => 'Bob',
            'email'  => 'bob@example.com',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_store_returns_403_when_comments_are_closed(): void
    {
        $this->createPost(['post_name' => 'closed-post', 'comment_status' => 'closed']);

        $response = $this->post(route('posts.comments.store', 'closed-post'), [
            'author'  => 'Bob',
            'email'   => 'bob@example.com',
            'content' => 'Let me in!',
        ]);

        $response->assertStatus(403);
    }

    public function test_store_returns_404_for_missing_post(): void
    {
        $response = $this->post(route('posts.comments.store', 'ghost-post'), [
            'author'  => 'Bob',
            'email'   => 'bob@example.com',
            'content' => 'Hello',
        ]);

        $response->assertStatus(404);
    }
}
