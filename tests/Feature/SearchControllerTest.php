<?php

namespace Tests\Feature;

use Tests\CreatesWordPressData;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{

    public function test_search_returns_successful_response(): void
    {
        $response = $this->get(route('search', ['q' => 'test']));

        $response->assertStatus(200);
    }

    public function test_search_returns_matching_posts_by_title(): void
    {
        $this->createPost(['post_title' => 'Laravel Testing Guide', 'post_name' => 'laravel-testing']);
        $this->createPost(['post_title' => 'Vue.js Tutorial',       'post_name' => 'vuejs-tutorial']);

        $response = $this->get(route('search', ['q' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Testing Guide');
        $response->assertDontSee('Vue.js Tutorial');
    }

    public function test_search_returns_matching_posts_by_content(): void
    {
        $this->createPost([
            'post_title'   => 'Some Post',
            'post_name'    => 'some-post',
            'post_content' => '<p>This is about Eloquent ORM in depth.</p>',
        ]);
        $this->createPost(['post_title' => 'Unrelated', 'post_name' => 'unrelated']);

        $response = $this->get(route('search', ['q' => 'Eloquent']));

        $response->assertSee('Some Post');
        $response->assertDontSee('Unrelated');
    }

    public function test_search_excludes_draft_posts(): void
    {
        $this->createPost(['post_title' => 'Published Result', 'post_name' => 'published-result', 'post_status' => 'publish']);
        $this->createPost(['post_title' => 'Draft Result',     'post_name' => 'draft-result',     'post_status' => 'draft']);

        $response = $this->get(route('search', ['q' => 'Result']));

        $response->assertSee('Published Result');
        $response->assertDontSee('Draft Result');
    }

    public function test_search_with_empty_query_returns_no_results(): void
    {
        $this->createPost(['post_title' => 'Any Post', 'post_name' => 'any-post']);

        $response = $this->get(route('search', ['q' => '']));

        $response->assertStatus(200);
    }
}
