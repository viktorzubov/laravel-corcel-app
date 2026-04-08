<?php

namespace Tests\Feature;

use Tests\CreatesWordPressData;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{

    public function test_show_returns_successful_response_for_existing_category(): void
    {
        $catId  = $this->createCategory('Web Design', 'web-design');
        $postId = $this->createPost(['post_title' => 'A Web Post', 'post_name' => 'a-web-post']);
        $this->attachTermToPost($postId, $catId, 'category');

        $response = $this->get(route('category.show', 'web-design'));

        $response->assertStatus(200);
    }

    public function test_show_returns_404_for_missing_category(): void
    {
        $response = $this->get(route('category.show', 'nonexistent-category'));

        $response->assertStatus(404);
    }

    public function test_show_lists_posts_belonging_to_category(): void
    {
        $catId       = $this->createCategory('Frontend', 'frontend');
        $inCatPostId = $this->createPost(['post_title' => 'CSS Tips', 'post_name' => 'css-tips']);
        $this->attachTermToPost($inCatPostId, $catId, 'category');
        $this->createPost(['post_title' => 'Backend Guide', 'post_name' => 'backend-guide']);

        $response = $this->get(route('category.show', 'frontend'));

        $response->assertStatus(200);
        $response->assertSee('CSS Tips');
        $response->assertDontSee('Backend Guide');
    }

    public function test_show_does_not_list_draft_posts_in_category(): void
    {
        $catId       = $this->createCategory('News', 'news');
        $publishedId = $this->createPost(['post_title' => 'Live News', 'post_name' => 'live-news', 'post_status' => 'publish']);
        $draftId     = $this->createPost(['post_title' => 'Draft News', 'post_name' => 'draft-news', 'post_status' => 'draft']);
        $this->attachTermToPost($publishedId, $catId, 'category');
        $this->attachTermToPost($draftId, $catId, 'category');

        $response = $this->get(route('category.show', 'news'));

        $response->assertSee('Live News');
        $response->assertDontSee('Draft News');
    }
}
