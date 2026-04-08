<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\CreatesWordPressData;
use Tests\TestCase;

class PostControllerTest extends TestCase
{

    public function test_index_returns_successful_response(): void
    {
        $this->createPost(['post_title' => 'Hello World', 'post_name' => 'hello-world']);

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee('Hello World');
    }

    public function test_index_shows_no_posts_when_database_is_empty(): void
    {
        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
    }

    public function test_index_does_not_show_draft_posts(): void
    {
        $this->createPost(['post_title' => 'Published Post', 'post_name' => 'published', 'post_status' => 'publish']);
        $this->createPost(['post_title' => 'Draft Post', 'post_name' => 'draft', 'post_status' => 'draft']);

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee('Published Post');
        $response->assertDontSee('Draft Post');
    }

    public function test_index_filters_posts_by_category(): void
    {
        $catId = $this->createCategory('UX Design', 'ux-design');
        $postInCategory = $this->createPost(['post_title' => 'UX Article', 'post_name' => 'ux-article']);
        $this->attachTermToPost($postInCategory, $catId, 'category');
        $this->createPost(['post_title' => 'Other Article', 'post_name' => 'other-article']);

        $response = $this->get(route('posts.index', ['category' => 'ux-design']));

        $response->assertStatus(200);
        $response->assertSee('UX Article');
        $response->assertDontSee('Other Article');
    }

    public function test_show_returns_successful_response_for_existing_post(): void
    {
        $this->createPost(['post_title' => 'My Article', 'post_name' => 'my-article']);

        $response = $this->get(route('posts.show', 'my-article'));

        $response->assertStatus(200);
        $response->assertSee('My Article');
    }

    public function test_show_returns_404_for_missing_post(): void
    {
        $response = $this->get(route('posts.show', 'does-not-exist'));

        $response->assertStatus(404);
    }

    public function test_show_returns_404_for_draft_post(): void
    {
        $this->createPost(['post_name' => 'hidden-draft', 'post_status' => 'draft']);

        $response = $this->get(route('posts.show', 'hidden-draft'));

        $response->assertStatus(404);
    }

    public function test_show_increments_view_count_on_each_visit(): void
    {
        $postId = $this->createPost(['post_name' => 'counted-post']);

        $this->get(route('posts.show', 'counted-post'));
        $this->get(route('posts.show', 'counted-post'));

        $viewCount = DB::connection('wordpress')
            ->table('postmeta')
            ->where('post_id', $postId)
            ->where('meta_key', 'post_views_count')
            ->value('meta_value');

        $this->assertEquals(2, (int) $viewCount);
    }

    public function test_show_displays_post_author_name(): void
    {
        $userId = $this->createUser(['user_nicename' => 'jane-doe', 'display_name' => 'Jane Doe']);
        $this->createPost([
            'post_name'   => 'authored-post',
            'post_author' => $userId,
        ]);

        $response = $this->get(route('posts.show', 'authored-post'));

        $response->assertStatus(200);
        $response->assertSee('Jane Doe');
    }

    public function test_show_displays_post_category(): void
    {
        $catId  = $this->createCategory('Technology', 'technology');
        $postId = $this->createPost(['post_name' => 'tech-post']);
        $this->attachTermToPost($postId, $catId, 'category');

        $response = $this->get(route('posts.show', 'tech-post'));

        $response->assertStatus(200);
        $response->assertSee('Technology');
    }
}
