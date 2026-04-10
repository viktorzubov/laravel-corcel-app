<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Tests\CreatesWordPressData;
use Tests\TestCase;

class PostObserverTest extends TestCase
{
    use CreatesWordPressData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWordPressDatabase();
    }

    public function test_creating_a_post_clears_caches(): void
    {
        Cache::put('post_categories', 'cached-categories', 3600);
        Cache::put('sitemap_xml', 'cached-xml', 86400);

        Post::create([
            'post_title' => 'New Post',
            'post_name' => 'new-post',
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_author' => 0,
            'post_date' => now()->format('Y-m-d H:i:s'),
            'post_date_gmt' => now()->utc()->format('Y-m-d H:i:s'),
            'post_modified' => now()->format('Y-m-d H:i:s'),
            'post_modified_gmt' => now()->utc()->format('Y-m-d H:i:s'),
            'post_content' => '',
            'post_excerpt' => '',
            'post_password' => '',
            'to_ping' => '',
            'pinged' => '',
            'post_content_filtered' => '',
            'comment_status' => 'open',
            'ping_status' => 'open',
            'post_parent' => 0,
            'menu_order' => 0,
            'post_mime_type' => '',
            'guid' => '',
            'comment_count' => 0,
        ]);

        $this->assertNull(Cache::get('post_categories'));
        $this->assertNull(Cache::get('sitemap_xml'));
    }

    public function test_updating_a_post_clears_caches(): void
    {
        $this->createPost(['post_title' => 'Post', 'post_name' => 'post']);
        $post = Post::type('post')->published()->slug('post')->firstOrFail();

        Cache::put('post_categories', 'cached-categories', 3600);
        Cache::put('sitemap_xml', 'cached-xml', 86400);

        $post->update(['post_title' => 'Updated Post']);

        $this->assertNull(Cache::get('post_categories'));
        $this->assertNull(Cache::get('sitemap_xml'));
    }

    public function test_deleting_a_post_clears_caches(): void
    {
        $this->createPost(['post_title' => 'Post', 'post_name' => 'post']);
        $post = Post::type('post')->published()->slug('post')->firstOrFail();

        Cache::put('post_categories', 'cached-categories', 3600);
        Cache::put('sitemap_xml', 'cached-xml', 86400);

        $post->delete();

        $this->assertNull(Cache::get('post_categories'));
        $this->assertNull(Cache::get('sitemap_xml'));
    }
}
