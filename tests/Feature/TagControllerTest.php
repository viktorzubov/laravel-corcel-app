<?php

namespace Tests\Feature;

use Tests\CreatesWordPressData;
use Tests\TestCase;

class TagControllerTest extends TestCase
{

    public function test_show_returns_successful_response_for_existing_tag(): void
    {
        $tagId  = $this->createTag('Accessibility', 'accessibility');
        $postId = $this->createPost(['post_title' => 'Tagged Post', 'post_name' => 'tagged-post']);
        $this->attachTermToPost($postId, $tagId, 'post_tag');

        $response = $this->get(route('tag.show', 'accessibility'));

        $response->assertStatus(200);
    }

    public function test_show_returns_404_for_missing_tag(): void
    {
        $response = $this->get(route('tag.show', 'nonexistent-tag'));

        $response->assertStatus(404);
    }

    public function test_show_lists_posts_with_tag(): void
    {
        $tagId       = $this->createTag('Performance', 'performance');
        $taggedPostId = $this->createPost(['post_title' => 'Speed Tips', 'post_name' => 'speed-tips']);
        $this->attachTermToPost($taggedPostId, $tagId, 'post_tag');
        $this->createPost(['post_title' => 'Unrelated Post', 'post_name' => 'unrelated-post']);

        $response = $this->get(route('tag.show', 'performance'));

        $response->assertStatus(200);
        $response->assertSee('Speed Tips');
        $response->assertDontSee('Unrelated Post');
    }
}
