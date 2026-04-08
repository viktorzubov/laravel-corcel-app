<?php

namespace Tests\Feature;

use Tests\CreatesWordPressData;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{

    public function test_show_returns_successful_response_for_existing_author(): void
    {
        $userId = $this->createUser(['user_nicename' => 'alice', 'display_name' => 'Alice Smith']);
        $this->createPost(['post_name' => 'alice-post', 'post_author' => $userId]);

        $response = $this->get(route('author.show', 'alice'));

        $response->assertStatus(200);
        $response->assertSee('Alice Smith');
    }

    public function test_show_returns_404_for_missing_author(): void
    {
        $response = $this->get(route('author.show', 'ghost-writer'));

        $response->assertStatus(404);
    }

    public function test_show_only_lists_posts_by_that_author(): void
    {
        $aliceId = $this->createUser(['user_nicename' => 'alice', 'display_name' => 'Alice']);
        $bobId   = $this->createUser(['user_login' => 'bob', 'user_nicename' => 'bob', 'user_email' => 'bob@example.com', 'display_name' => 'Bob']);

        $this->createPost(['post_title' => 'Alice Post', 'post_name' => 'alice-post', 'post_author' => $aliceId]);
        $this->createPost(['post_title' => 'Bob Post',   'post_name' => 'bob-post',   'post_author' => $bobId]);

        $response = $this->get(route('author.show', 'alice'));

        $response->assertSee('Alice Post');
        $response->assertDontSee('Bob Post');
    }

    public function test_show_does_not_list_draft_posts_for_author(): void
    {
        $userId = $this->createUser(['user_nicename' => 'carol', 'display_name' => 'Carol']);
        $this->createPost(['post_title' => 'Published',   'post_name' => 'published-carol',   'post_author' => $userId, 'post_status' => 'publish']);
        $this->createPost(['post_title' => 'Draft Hidden', 'post_name' => 'draft-carol', 'post_author' => $userId, 'post_status' => 'draft']);

        $response = $this->get(route('author.show', 'carol'));

        $response->assertSee('Published');
        $response->assertDontSee('Draft Hidden');
    }
}
